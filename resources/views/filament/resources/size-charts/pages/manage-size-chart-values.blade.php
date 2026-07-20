<x-filament-panels::page>
    <x-filament::section :heading="$this->record->name" description="Enter the measurement values for each size.">

        <div style="
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid var(--gray-200, #e5e7eb);
            background-color: var(--card-bg, #ffffff);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        " class="custom-table-container">

            <table
                style="width: 100%; border-collapse: collapse; font-family: inherit; font-size: 0.875rem; text-align: left;">

                <thead>
                    <tr style="
                        background-color: var(--table-header-bg, #f9fafb);
                        border-bottom: 1px solid var(--table-border, #e5e7eb);
                    ">
                        <th style="
                            width: 130px;
                            padding: 16px 24px;
                            font-weight: 600;
                            color: var(--table-text-dark, #111827);
                            letter-spacing: 0.025em;
                        ">
                            Size
                        </th>
                        @foreach ($measurements as $measurement)
                            <th style="min-w: 150px; padding: 16px; text-align: center;">
                                <div style="font-weight: 600; color: var(--table-text-dark, #111827); line-height: 1.25;">
                                    {{ $measurement->name }}
                                </div>
                                <div
                                    style="font-size: 0.75rem; font-weight: 500; color: #9ca3af; margin-top: 2px; letter-spacing: 0.05em;">
                                    {{ strtoupper($measurement->unit) }}
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody style="color: var(--table-text-medium, #374151);">
                    @foreach ($sizes as $size)
                        <tr style="
                                        border-bottom: 1px solid var(--table-border, #f3f4f6);
                                        transition: background-color 0.15s ease-in-out;
                                    " onmouseover="this.style.backgroundColor='var(--table-hover, #f9fafb)'"
                            onmouseout="this.style.backgroundColor='transparent'">

                            <td style="
                                            padding: 16px 24px;
                                            font-weight: 600;
                                            color: var(--table-text-dark, #111827);
                                            background-color: var(--side-column-bg, #fcfcfd);
                                        ">
                                {{ $size->name }}
                            </td>

                            @foreach ($measurements as $measurement)
                                <td style="padding: 10px; text-align: center;">
                                    <input type="text" step="0.01"
                                        wire:model.blur="values.{{ $size->id }}.{{ $measurement->id }}" placeholder="0.00"
                                        style="
                                                                    width: 100%;
                                                                    max-width: 120px;
                                                                    padding: 8px 12px;
                                                                    font-size: 0.875rem;
                                                                    font-weight: 500;
                                                                    text-align: center;
                                                                    border: 1px solid var(--input-border, #d1d5db);
                                                                    border-radius: 8px;
                                                                    background-color: var(--input-bg, #ffffff);
                                                                    color: var(--table-text-dark, #111827);
                                                                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                                                                    outline: none;
                                                                    transition: border-color 0.2s, box-shadow 0.2s;
                                                                "
                                        onfocus="this.style.borderColor='var(--primary-color, #4f46e5)'; this.style.boxShadow='0 0 0 3px var(--primary-focus, rgba(79, 70, 229, 0.15))';"
                                        onblur="this.style.borderColor='var(--input-border, #d1d5db)'; this.style.boxShadow='0 1px 2px 0 rgba(0, 0, 0, 0.05)';" />
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        {{-- Filament-এর ডার্ক মোডের সাথে শতভাগ সিঙ্ক রাখার জন্য এই ছোট ইনলাইন স্টাইল ব্লকটি ব্যবহার করা হয়েছে --}}
        <style>
            :root {
                --card-bg: #ffffff;
                --gray-200: #e5e7eb;
                --table-header-bg: #f9fafb;
                --table-border: #e5e7eb;
                --table-text-dark: #111827;
                --table-text-medium: #374151;
                --table-hover: #f9fafb;
                --side-column-bg: #fcfcfd;
                --input-border: #d1d5db;
                --input-bg: #ffffff;
                --primary-color: #4f46e5;
                --primary-focus: rgba(79, 70, 229, 0.15);
            }

            .dark {
                --card-bg: #111827;
                --gray-200: rgba(255, 255, 255, 0.1);
                --table-header-bg: rgba(255, 255, 255, 0.03);
                --table-border: rgba(255, 255, 255, 0.05);
                --table-text-dark: #ffffff;
                --table-text-medium: #d1d5db;
                --table-hover: rgba(255, 255, 255, 0.02);
                --side-column-bg: transparent;
                --input-border: rgba(255, 255, 255, 0.15);
                --input-bg: #1f2937;
                --primary-color: #6366f1;
                --primary-focus: rgba(99, 102, 241, 0.2);
            }
        </style>

    </x-filament::section>
</x-filament-panels::page>