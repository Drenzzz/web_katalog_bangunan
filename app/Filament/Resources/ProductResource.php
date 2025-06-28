<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront'; // Ikon baru
    protected static ?int $navigationSort = 2; // Urutan di sidebar

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Informasi Produk')->schema([
                        Forms\Components\TextInput::make('name')->required()->lazy()
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')->required(),
                        Forms\Components\RichEditor::make('description')->columnSpanFull(),
                    ])->columns(2),

                    Forms\Components\Section::make('Harga & Stok')->schema([
                        Forms\Components\TextInput::make('price')->numeric()->prefix('Rp')->required(),
                        Forms\Components\TextInput::make('stock')->numeric()->required(),
                        Select::make('unit_id')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Satuan'),
                    ])->columns(3),
                ])->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()->schema([
                    Forms\Components\Section::make('Asosiasi')->schema([
                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
                    Forms\Components\Section::make('Gambar')->schema([
                        FileUpload::make('image')
                            ->directory('product-images')
                            ->image()
                            ->required(),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Produk')->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('slug'),
                    Infolists\Components\TextEntry::make('description')->html(),
                ])->columns(2),

                Infolists\Components\Section::make('Harga, Stok & Asosiasi')->schema([
                    Infolists\Components\TextEntry::make('price')->money('IDR'),
                    Infolists\Components\TextEntry::make('stock'),
                    Infolists\Components\TextEntry::make('category.name'),
                    Infolists\Components\TextEntry::make('brand.name'),
                    Infolists\Components\TextEntry::make('unit.name'),
                ])->columns(3),

                Infolists\Components\Section::make('Gambar')->schema([
                    Infolists\Components\ImageEntry::make('image')->height(200),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom untuk menampilkan gambar utama
                ImageColumn::make('image')
                    ->label('Gambar'),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                // Menampilkan nama kategori dari relasi
                Tables\Columns\TextColumn::make('category.name')->sortable(),
                Tables\Columns\TextColumn::make('price')->money('IDR')->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),

            ])
            ->actions([
                // Grup untuk aksi-aksi utama
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(), // <-- Tombol View Detail
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(), // Ini adalah Soft Delete
                    Tables\Actions\ForceDeleteAction::make(), // <-- Tombol Hapus Permanen
                    Tables\Actions\RestoreAction::make(), // <-- Tombol Kembalikan
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
