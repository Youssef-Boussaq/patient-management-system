<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Filament\Resources\PatientResource\RelationManagers\TreatmentsRelationManager;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'cat' => 'Cat',
                        'dog' => 'Dog',
                        'rabbit' => 'Rabbit',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date_of_birth')
                    ->required()
                    ->maxDate(now()),

                // إعداد الحقل Select لاختيار المالك
                Forms\Components\Select::make('owner_id')
                    ->relationship('owner', 'name') // تعيين العلاقة بين المريض والمالك
                    ->searchable() // السماح بالبحث
                    ->preload() // تحميل الخيارات مسبقاً لتسريع الأداء
                    ->createOptionForm([ // إنشاء نموذج منبثق لإضافة مالك جديد
                        Forms\Components\TextInput::make('name') // حقل الاسم
                            ->required() // إلزامية الحقل
                            ->maxLength(255), // الحد الأقصى للطول
                        Forms\Components\TextInput::make('email') // حقل البريد الإلكتروني
                            ->label('Email address') // تعديل التسمية
                            ->email() // التأكد من أن المدخلات هي عنوان بريد إلكتروني صالح
                            ->required() // إلزامية الحقل
                            ->maxLength(255), // الحد الأقصى للطول
                        Forms\Components\TextInput::make('phone') // حقل رقم الهاتف
                            ->label('Phone number') // تعديل التسمية
                            ->tel() // التأكد من أن المدخلات هي رقم هاتف صالح
                            ->required(), // إلزامية الحقل
                    ])
                    ->required(), // إلزامية اختيار المالك

                

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('date_of_birth'),
                Tables\Columns\TextColumn::make('owner.name'),
                //
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                ->options([
                    'cat' => '🐈​',
                    'dog' => 'Dog',
                    'rabbit' => 'Rabbit',
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TreatmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
