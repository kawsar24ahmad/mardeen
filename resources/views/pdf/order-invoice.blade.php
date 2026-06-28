<!DOCTYPE html>
<html lang="en" style="background-color: #ffffff; height: 100%;">

<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #334155;
            margin: 0;
            padding: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .table-layout {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .table-layout td {
            padding: 0;
            vertical-align: top;
        }

        .invoice-items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .invoice-items th {
            background-color: #4f46e5;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 11px;
            padding: 10px 15px;
            letter-spacing: 0.5px;
        }

        .invoice-items td {
            padding: 12px 15px;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
</head>

<body>

    <div
        style="max-width: 800px; margin: 0 auto; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">

        <div style="height: 6px; background-color: #4f46e5;"></div>

        <div style="background-color: #0f172a; color: #ffffff; padding: 35px;">
            <table class="table-layout">
                <tr>
                    <td>
                        <div
                            style="display: inline-block; width: 45px; height: 45px; line-height: 45px; text-align: center; background-color: #f59e0b; color: #0f172a; font-weight: bold; font-size: 20px; border-radius: 8px; margin-bottom: 10px;">
                            {{ strtoupper(substr(config('app.name', 'P'), 0, 1)) }}
                        </div>
                        <h2 style="font-size: 24px; font-weight: bold; margin: 0 0 2px 0; color: #ffffff;">
                            {{ config('app.name') }}
                        </h2>
                        <p
                            style="font-size: 11px; font-weight: 600; color: #a5b4fc; text-transform: uppercase; letter-spacing: 1px; margin: 0;">
                            Professional Invoice
                        </p>
                    </td>
                    <td style="text-align: right;">
                        <span
                            style="display: inline-block; padding: 3px 10px; background-color: rgba(79, 70, 229, 0.2); color: #a5b4fc; border: 1px solid rgba(79, 70, 229, 0.3); border-radius: 50px; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px;">
                            Invoice
                        </span>
                        <h1
                            style="font-size: 20px; font-weight: bold; margin: 0 0 5px 0; color: #ffffff; tracking-tight">
                            Order #{{ $order->id }}
                        </h1>
                        <p style="font-size: 13px; color: #cbd5e1; margin: 0; font-weight: 500;">
                            {{ $order->created_at->format('d M Y') }}
                            <span style="color: #6366f1; margin: 0 4px;">•</span>
                            {{ $order->created_at->format('h:i A') }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div style="padding: 35px;">

            <table class="table-layout" style="margin-bottom: 35px;">
                <tr>
                    <td
                        style="width: 48%; background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 20px;">
                        <h3
                            style="font-size: 11px; font-weight: bold; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 12px 0;">
                            • Billed To
                        </h3>
                        <p style="font-size: 14px; font-weight: bold; color: #0f172a; margin: 0 0 3px 0;">
                            {{ $order->customer->name }}</p>
                        <p style="font-size: 13px; color: #475569; margin: 0 0 3px 0;">{{ $order->customer->email }}</p>
                        <p style="font-size: 13px; color: #475569; margin: 0 0 12px 0;">{{ $order->shipping_phone }}</p>

                        <div>
                            <span
                                style="display: inline-block; padding: 3px 8px; font-size: 11px; font-weight: bold; text-transform: uppercase; background-color: #dcfce7; color: #166534; border: 1px solid #bbf7d0; border-radius: 6px;">
                                {{ $order->status }}
                            </span>
                        </div>
                    </td>
                    <td style="width: 4%;"></td>
                    <td
                        style="width: 48%; background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 20px;">
                        <h3
                            style="font-size: 11px; font-weight: bold; color: #4f46e5; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 12px 0;">
                            • Shipped To
                        </h3>
                        <p style="font-size: 14px; font-weight: bold; color: #0f172a; margin: 0 0 3px 0;">
                            {{ $order->shipping_full_name }}</p>
                        <p style="font-size: 13px; color: #475569; line-height: 1.5; margin: 0;">
                            {{ $order->shipping_address_line_1 }}</p>
                    </td>
                </tr>
            </table>

            <h3
                style="font-size: 13px; font-weight: bold; color: #0f172a; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 10px 0; padding-bottom: 8px; border-b: 1px solid #e2e8f0;">
                Order Summary
            </h3>

            <div style="border: 1px solid #f1f5f9; border-radius: 8px; overflow: hidden; margin-bottom: 30px;">
                <table class="invoice-items">
                    <thead>
                        <tr>
                            <th style="text-align: left; width: 45%;">Product</th>
                            <th style="text-align: center; width: 12%;">Qty</th>
                            <th style="text-align: right; width: 20%;">Price</th>
                            <th style="text-align: right; width: 23%;">Total</th>
                        </tr>
                    </thead>
                    <tbody style="background-color: #ffffff;">
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $item->product->name }}</div>
                                    @if($item->variant || $item->variant_name)
                                        <span
                                            style="display: inline-block; margin-top: 4px; padding: 1px 6px; border-radius: 50px; font-size: 10px; font-weight: 500; background-color: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;">
                                            {{ $item->variant?->display_label ?? $item->variant_name }}
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center; font-weight: bold; color: #334155;">
                                    {{ $item->quantity }}
                                </td>
                                <td style="text-align: right; color: #475569;">
                                    BDT {{ number_format($item->price, 2) }}
                                </td>
                                <td style="text-align: right; font-weight: bold; color: #0f172a;">
                                    BDT {{ number_format($item->price * $item->quantity, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <table class="table-layout">
                <tr>
                    <td style="width: 50%; font-size: 11px; color: #64748b; line-height: 1.6; padding-right: 20px;">
                        <p style="font-weight: 600; color: #475569; margin: 0 0 4px 0;">Payment Information:</p>
                        <p style="margin: 0;">All values are rendered securely in Bangladeshi Taka (BDT). For financial
                            inquiries, contact platform support channels.</p>
                    </td>

                    <td style="width: 50%;">
                        <div
                            style="background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 20px;">

                            <table class="table-layout" style="font-size: 13px; margin-bottom: 8px;">
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Subtotal</td>
                                    <td style="text-align: right; font-weight: bold; color: #0f172a;">BDT
                                        {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                            </table>

                            <table class="table-layout" style="font-size: 13px; margin-bottom: 8px;">
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Discount</td>
                                    <td style="text-align: right; font-weight: bold; color: #dc2626;">- BDT
                                        {{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                            </table>

                            <table class="table-layout" style="font-size: 13px; margin-bottom: 8px;">
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Shipping</td>
                                    <td style="text-align: right; font-weight: bold; color: #0f172a;">BDT
                                        {{ number_format($order->shipping_cost, 2) }}</td>
                                </tr>
                            </table>

                            <table class="table-layout" style="font-size: 13px; margin-bottom: 12px;">
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Tax</td>
                                    <td style="text-align: right; font-weight: bold; color: #0f172a;">BDT
                                        {{ number_format($order->tax_amount ?? 0, 2) }}</td>
                                </tr>
                            </table>

                            <div style="border-top: 1px dashed #cbd5e1; margin-bottom: 12px; height: 1px;"></div>

                            <div
                                style="background-color: #0f172a; border-radius: 8px; padding: 12px 15px; color: #ffffff;">
                                <table class="table-layout">
                                    <tr>
                                        <td
                                            style="font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #c7d2fe; vertical-align: middle;">
                                            Grand Total</td>
                                        <td
                                            style="text-align: right; font-size: 18px; font-weight: 800; color: #ffffff; vertical-align: middle;">
                                            BDT {{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </td>
                </tr>
            </table>

            <div
                style="margin-top: 40px; padding-top: 25px; border-top: 1px solid #f1f5f9; text-align: center; color: #64748b; font-size: 12px;">
                <p style="font-size: 14px; font-weight: bold; color: #0f172a; margin: 0 0 5px 0;">Thank you for your
                    purchase!</p>
                <p style="margin: 0; font-weight: 500;">
                    <span>We appreciate your business</span>
                    <span style="color: #cbd5e1; margin: 0 6px;">•</span>
                    <span style="color: #334155; font-weight: 600;">{{ config('app.name') }}</span>
                    <span style="color: #cbd5e1; margin: 0 6px;">•</span>
                    <span>{{ $order->created_at->format('d M Y') }}</span>
                </p>
            </div>

        </div>
    </div>

</body>

</html>