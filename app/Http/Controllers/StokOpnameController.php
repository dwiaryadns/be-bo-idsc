<?php

namespace App\Http\Controllers;

use App\Models\FasyankesWarehouse;
use App\Models\StockBarang;
use App\Models\StockGudang;
use App\Models\StokOpname;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StokOpnameController extends Controller
{
    public function barang(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $fasyankesId = $request->get('fasyankes_id', '');
        $warehouseId = $request->get('warehouse_id', '');

        Log::info($request->all());

        if ($warehouseId || $fasyankesId) {
            if ($fasyankesId) {
                $query = StockBarang::query();
                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                            ->orWhereHas('barang', function ($q) use ($search) {
                                $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                            })
                            ->orWhereHas('supplier', function ($q) use ($search) {
                                $q->whereRaw('LOWER(nama_supplier) LIKE ?', ['%' . strtolower($search) . '%']);
                            });
                    });
                }

                $query->whereHas('fasyankes_warehouse.fasyankes', function ($q) use ($fasyankesId) {
                    $q->where('fasyankesId', $fasyankesId);
                });

                $barangs = $query->with([
                    'latestStokOpname',
                    'barang' => function ($q) {
                        $q->select('barang_id', 'nama_barang', 'supplier_id', 'harga_beli', 'harga_jual', 'satuan');
                    },
                    'barang.supplier' => function ($q) {
                        $q->select('supplier_id', 'nama_supplier');
                    },
                    'fasyankes_warehouse.fasyankes' => function ($q) {
                        $q->select('fasyankesId', 'name');
                    },
                    'fasyankes_warehouse.warehouse' => function ($q) {
                        $q->select('id', 'name');
                    }
                ])->whereHas('barang.supplier', function ($q) use ($bo) {
                    $q->where('bisnis_owner_id', $bo->id);
                })->paginate($perPage, ['*'], 'page', $page);
            } else {
                $query = StockGudang::query();
                if (!empty($search)) {
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                            ->orWhereHas('barang', function ($q) use ($search) {
                                $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                            });
                    });
                }

                $query->where('warehouse_id', $warehouseId);

                $barangs = $query->with([
                    'latestStokOpname',
                    'barang' => function ($q) {
                        $q->select('barang_id', 'nama_barang', 'supplier_id', 'harga_beli', 'harga_jual', 'satuan');
                    },
                    'barang.supplier' => function ($q) {
                        $q->select('supplier_id', 'nama_supplier');
                    },
                    'warehouse' => function ($q) {
                        $q->select('name', 'id');
                    }

                ])->whereHas('barang.supplier', function ($q) use ($bo) {
                    $q->where('bisnis_owner_id', $bo->id);
                })->paginate($perPage, ['*'], 'page', $page);
            }
        } else {
            $stockBarangQuery = StockBarang::query();
            $stockGudangQuery = StockGudang::query();

            if (!empty($search)) {
                $stockBarangQuery->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereHas('barang', function ($q) use ($search) {
                            $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                        })
                        ->orWhereHas('supplier', function ($q) use ($search) {
                            $q->whereRaw('LOWER(nama_supplier) LIKE ?', ['%' . strtolower($search) . '%']);
                        });
                });

                $stockGudangQuery->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereHas('barang', function ($q) use ($search) {
                            $q->whereRaw('LOWER(nama_barang) LIKE ?', ['%' . strtolower($search) . '%']);
                        });
                });
            }

            $stockBarang = $stockBarangQuery->with([
                'latestStokOpname',

                'barang' => function ($q) {
                    $q->select('barang_id', 'nama_barang', 'supplier_id', 'harga_beli', 'harga_jual', 'satuan');
                },
                'barang.supplier' => function ($q) {
                    $q->select('supplier_id', 'nama_supplier');
                },
                'fasyankes_warehouse.fasyankes' => function ($q) {
                    $q->select('fasyankesId', 'name');
                },
                'fasyankes_warehouse.warehouse' => function ($q) {
                    $q->select('id', 'name');
                }
            ])->whereHas('barang.supplier', function ($q) use ($bo) {
                $q->where('bisnis_owner_id', $bo->id);
            })->get();

            $stockGudang = $stockGudangQuery->with([
                'latestStokOpname',
                'barang' => function ($q) {
                    $q->select('barang_id', 'nama_barang', 'supplier_id', 'harga_beli', 'harga_jual', 'satuan');
                },
                'barang.supplier' => function ($q) {
                    $q->select('supplier_id', 'nama_supplier');
                },
                'warehouse' => function ($q) {
                    $q->select('name', 'id');
                }
            ])->whereHas('barang.supplier', function ($q) use ($bo) {
                $q->where('bisnis_owner_id', $bo->id);
            })->get();

            $combined = $stockBarang->merge($stockGudang);

            $pageData = $combined->forPage($page, $perPage)->values();
            $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
                $pageData,
                $combined->count(),
                $perPage,
                $page
            );

            return response()->json([
                'status' => true,
                'message' => 'Success Get Barang from Both Stocks',
                'data' => $paginatedData
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Get Stok ',
            'data' => $barangs
        ], 200);
    }

    public function getStokOpname(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }

        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $search = $request->get('search', '');
        $fasyankes_id = $request->get('fasyankes_id', '');
        $warehouse_id = $request->get('warehouse_id', '');
        $query = StokOpname::query();
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(barang_id) LIKE ?', ['%' . strtolower($search) . '%'])
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->whereRaw('LOWER(nama_baran/g) LIKE ?', ['%' . strtolower($search) . '%']);
                    });
            });
        }
        if ($fasyankes_id || $warehouse_id) {
            if ($warehouse_id && $fasyankes_id) {
                $query->whereHas('stok_barang.fasyankes_warehouse', function ($q) use ($fasyankes_id, $warehouse_id) {
                    $q->where('fasyankes_id', $fasyankes_id);
                });
            } else if ($warehouse_id) {
                $query->whereHas('stok_gudang.warehouse', function ($q) use ($warehouse_id) {
                    $q->where('warehouse_id', $warehouse_id);
                });
            }
        }
        $opname = $query->with([
            'barang' => function ($q) {
                $q->select('barang_id', 'nama_barang', 'supplier_id', 'harga_beli', 'harga_jual', 'satuan'); // Tambahkan 'supplier_id'
            },
            'barang.supplier' => function ($q) {
                $q->select('supplier_id', 'nama_supplier');
            },
            'stok_gudang.warehouse',
            'stok_barang.fasyankes_warehouse.fasyankes'
        ])->whereHas('barang.supplier', function ($q) use ($bo) {
            $q->where('bisnis_owner_id', $bo->id);
        })->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'message' => 'Success Get Opaname',
            'data' => $opname
        ], 200);
    }

    public function storeOpname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang_id' => 'required',
            'jml_tercatat' => 'required|numeric',
            'jml_fisik' => 'required|numeric',
            'jml_penyesuaian' => 'required|numeric',
            'keterangan' => 'required|string'
        ]);
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json([
                'status' => false,
                'errors' => $errors,
                'message' => 'Gagal'
            ], 422);
        }

        try {
            $opname = StokOpname::create([
                'stok_opname_id' => 'SOID-' . date('Y') . date('m') . str_pad(StokOpname::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'barang_id' => $request->barang_id,
                'petugas' => $request->petugas,
                'jml_tercatat' => $request->jml_tercatat,
                'jml_fisik' => $request->jml_fisik,
                'jml_penyesuaian' => $request->jml_penyesuaian,
                'keterangan' => $request->keterangan,
                'tanggal_opname' => Carbon::now(),
                'stock_gudang_id' => $request->stock_gudang_id ?? null,
                'stok_barang_id' => $request->stok_barang_id ?? null,
            ]);

            if ($request->stock_gudang_id) {
                $stockGudang = StockGudang::find($request->stock_gudang_id);
                $stockGudang->update([
                    'stok' => $request->jml_fisik
                ]);
            }
            if ($request->stok_barang_id) {
                $stokBarang = StockBarang::find($request->stok_barang_id);
                $stokBarang->update([
                    'stok' => $request->jml_fisik
                ]);
            }
            // log_actvity jika stok barang maka get fasyankes nya jika stok gudang maka get gudang
            if ($request->stok_barang_id) {
                log_activity("Melakukan Stock Opname oleh $request->petugas ke Fasyankes {$stokBarang->fasyankes_warehouse->fasyankes->name}", 'Stock Opname', Auth::guard('bisnis_owner')->user()->name, 1);
            } else {
                log_activity("Melakukan Stock Opname oleh $request->petugas ke Gudang {$stockGudang->warehouse->name}", 'Stock Opname', Auth::guard('bisnis_owner')->user()->name, 1);
            }
            // log_activity("Melakukan Opname oleh $request->petugas ke ")
            return response()->json([
                'status' => true,
                'message' => 'Berhasil',
            ]);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Gagal',
            ]);
        }
    }
}
