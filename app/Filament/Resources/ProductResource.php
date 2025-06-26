<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront'; // Ikon baru
    protected static ?int $navigationSort = 2; // Urutan di sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([ // Grup untuk kolom utama
                    Forms\Components\Section::make('Informasi Produk')->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\Textarea::make('description')->rows(5),
                    ]),
                    Forms\Components\Section::make('Gambar')->schema([
                        // Ini adalah field untuk upload gambar dari Spatie Media Library
                        Forms\Components\SpatieMediaLibraryFileUpload::make('product_images')
                            ->label('Gambar Produk')
                            ->multiple() // Bisa upload banyak gambar
                            ->reorderable()
                            ->image()
                            ->collection('products'), // Simpan ke collection 'products'
                    ]),
                ])->columnSpan(2),

                Forms\Components\Group::make()->schema([ // Grup untuk sidebar
                    Forms\Components\Section::make('Detail Harga & Stok')->schema([
                        Forms\Components\TextInput::make('price')->required()->numeric()->prefix('Rp'),
                        Forms\Components\TextInput::make('sku')->label('SKU (Kode Unik)')->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('unit')->required()->default('pcs'),
                    ]),
                    Forms\Components\Section::make('Asosiasi')->schema([
                        // Dropdown untuk memilih Kategori
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan gambar utama
                Tables\Columns\SpatieMediaLibraryImageColumn::make('main_image')
                    ->label('Gambar')
                    ->collection('products'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                // Menampilkan nama kategori dari relasi
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('price')->money('IDR')->sortable(),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
