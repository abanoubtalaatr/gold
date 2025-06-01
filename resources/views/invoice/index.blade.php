<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('mobile.invoice_electronic')</title>
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
            <h1 class="text-center text-xl font-bold mb-6">@lang('mobile.invoice_electronic')</h1>
            
            <!-- QR Code -->
            <div class="flex justify-center mb-6">
                <div class="w-40 h-40">
                    <img src="{{ asset('public/qrcode.png') }}" alt="QR Code" class="w-full h-full">
                </div>
            </div>
            
            <!-- Invoice Details -->
            <div class="space-y-3">
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.name_of_the_piece')</div>
                    <div>{{ $order->goldPiece->name }}</div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.weight_of_the_piece')</div>
                    <div>{{ $order->goldPiece->weight }} @lang('mobile.gram')</div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.karat')</div>
                    <div>{{ $order->goldPiece->carat }} </div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.store')</div>
                    <div>{{ $order?->vendor?->name }}</div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.sale_price')</div>
                    <div>{{ $order->goldPiece->sale_price }} @lang('mobile.sar')</div>
                </div>
                
                <div class="flex justify-between">
                    <div class="font-bold">@lang('mobile.sale_date')</div>
                    <div>{{ $order->created_at->translatedFormat('d,F Y') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Print Button -->
        <button onclick="window.print()" class="print-button w-full bg-black text-white py-4 font-bold text-center">
            @lang('mobile.print_the_invoice_pdf')
        </button>
    </div>

    <script>
        // Additional JavaScript can be added here if needed
    </script>
</body>
</html>
