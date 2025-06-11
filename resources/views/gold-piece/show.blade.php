<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('mobile.gold_piece_details')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap');
        
        body {
            font-family: 'Tajawal', sans-serif;
        }
        
        @media print {
            .print-button {
                display: none;
            }
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen p-4">
    
    <div class="bg-white rounded-3xl shadow-lg max-w-sm w-full overflow-hidden">
        <!-- Header -->
        <div class="p-6 pb-4">
            <h1 class="text-center text-xl font-bold mb-6">@lang('mobile.gold_piece_details')</h1>
            
            <!-- Gold Piece Images -->
            @if($goldPiece->getMedia('images')->count() > 0)
                <div class="flex justify-center mb-6">
                    <div class="w-40 h-40 rounded-lg overflow-hidden">
                        <img src="{{ $goldPiece->getFirstMediaUrl('images') }}" alt="{{ $goldPiece->name }}" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif
            
            <!-- QR Code -->
            @if($goldPiece->qr_code)
                <div class="flex justify-center mb-6">
                    <div class="w-32 h-32">
                        <img src="data:image/png;base64,{{ $goldPiece->qr_code }}" alt="QR Code" class="w-full h-full">
                    </div>
                </div>
            @endif
            
            <!-- Gold Piece Details -->
            <div class="space-y-3">
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.name_of_the_piece')</div>
                    <div>{{ $goldPiece->name }}</div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.weight_of_the_piece')</div>
                    <div>{{ $goldPiece->weight }} @lang('mobile.gram')</div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.karat')</div>
                    <div>{{ $goldPiece->carat }}</div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.type')</div>
                    <div>
                        @if($goldPiece->type === 'for_rent')
                            @lang('mobile.for_rent')
                        @else
                            @lang('mobile.for_sale')
                        @endif
                    </div>
                </div>
                
                @if($goldPiece->type === 'for_rent')
                    <div class="flex justify-between">
                        <div class="font-bold">@lang('mobile.rental_price_per_day')</div>
                        <div>{{ $goldPiece->rental_price_per_day }} @lang('mobile.sar')</div>
                    </div>
                    
                    <div class="flex justify-between">
                        <div class="font-bold">@lang('mobile.deposit_amount')</div>
                        <div>{{ $goldPiece->deposit_amount }} @lang('mobile.sar')</div>
                    </div>
                @else
                    <div class="flex justify-between">
                        <div class="font-bold">@lang('mobile.sale_price')</div>
                        <div>{{ $goldPiece->sale_price }} @lang('mobile.sar')</div>
                    </div>
                @endif
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.owner')</div>
                    <div>{{ $goldPiece->user->name }}</div>
                </div>
                
                @if($goldPiece->description)
                    <div class="flex justify-between">
                        <div class="font-bold">@lang('mobile.description')</div>
                        <div class="text-right max-w-xs">{{ $goldPiece->description }}</div>
                    </div>
                @endif
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.status')</div>
                    <div>
                        @switch($goldPiece->status)
                            @case('pending')
                                <span class="text-yellow-600">@lang('mobile.pending')</span>
                                @break
                            @case('available')
                                <span class="text-green-600">@lang('mobile.available')</span>
                                @break
                            @case('rented')
                                <span class="text-blue-600">@lang('mobile.rented')</span>
                                @break
                            @case('sold')
                                <span class="text-red-600">@lang('mobile.sold')</span>
                                @break
                            @default
                                {{ $goldPiece->status }}
                        @endswitch
                    </div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.created_date')</div>
                    <div>{{ $goldPiece->created_at->translatedFormat('d F Y') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Print Button -->
        <button onclick="window.print()" class="print-button w-full bg-black text-white py-4 font-bold text-center">
            @lang('mobile.print_details')
        </button>
    </div>

    <script>
        // Additional JavaScript can be added here if needed
    </script>
</body>
</html> 