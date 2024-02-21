@php
    $value = $row->{$field->label} ?? '';
    $type = $field->type;
    $meta = $field->meta ?? null;
    $tooltip = $value->{$meta->mainField ?? ''} ?? '';

    if ($type == 'reference') {
        $type = $meta->type;
        $value = $value->{$meta->field} ?? '';
    }

    $centered = in_array($type, $centeredFields);

    $value = match ($type) {
        'image' => (function () use ($value, $meta) {
            $sizing = $meta && ($meta->face ?? null) ? ' w-[40px] aspect-square rounded-full' : ' w-[60px] aspect-[2/1.3] rounded';
            return "<img class='$sizing inline-block object-cover object-center' src='$value' />";
        })(),

        'video' => (function () use ($value, $meta) {
            $videoId = (function ($url) {
                preg_match('/(?:\/|=)(.{11})(?:$|&|\?)/', $url, $matches);
                return $matches[1] ?? '';
            })($value);

            $src = "https://i.ytimg.com/vi/$videoId/hqdefault.jpg";

            $icon = "<svg class='w-5 h-5 text-red-500' fill='currentColor' width='20px' viewBox='0 0 24 24'>
                <rect fill='#fff' x='5' y='5' width='12' height='12' />
                <path d='M23.495 6.205a3.007 3.007 0 0 0-2.088-2.088c-1.87-.501-9.396-.501-9.396-.501s-7.507-.01-9.396.501A3.007 3.007 0 0 0 .527 6.205a31.247 31.247 0 0 0-.522 5.805 31.247 31.247 0 0 0 .522 5.783 3.007 3.007 0 0 0 2.088 2.088c1.868.502 9.396.502 9.396.502s7.506 0 9.396-.502a3.007 3.007 0 0 0 2.088-2.088 31.247 31.247 0 0 0 .5-5.783 31.247 31.247 0 0 0-.5-5.805zM9.609 15.601V8.408l6.264 3.602z'/>
            </svg>";

            // $icon = "<svg width='21' class='text-white' fill='currentColor' viewBox='0 0 24 24'><path d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z'/></svg>";

            return "<a title='$videoId' target='_blank' href='$value'
                class='inline-flex relative rounded overflow-hidden'>
                    <img class='w-[60px] aspect-[2/1.3] rounded' src='$src' />

                    <span class='bg-black/50 absolute inset-0 flex items-center justify-center'>
                        $icon
                    </span>
            </a>";
        })(),

        'date' => (function () use ($value) {
            $date = \Carbon\Carbon::parse($value);
            return $date->format($date->year === now()->year ? 'M d, g:i A' : 'M d, Y, g:i A');
        })(),

        'status' => (function () use ($value, $field, $row, $centered) {
            $color = $row->{$field->label . 'Meta'}->color ?? '';

            // $color = $color ? "<span class='w-2.5 h-2.5 rounded-full' style='background: $color;'></span>" : '';
            // return "<span class='inline-flex items-center gap-1.5'>$color$value</span>";

            return "<span class='inline-block rounded-full px-2.5 py-1.5 text-white text-xs/none leading-none font-medium'
                style='background: $color'>
                $value
            </span>";
        })(),

        'rating' => (function () use ($value, $meta) {
            $stars = collect()
                ->range(0, intval($meta->outOf) - 1)
                ->map(function ($index) use ($value) {
                    if ($value >= $index + 1) {
                        return '<svg fill="#e9b531" height="24" width="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63L2 9.24l5.46 4.73L5.82 21z"/></svg>';
                    }

                    if ($index - $value == -0.5) {
                        return '<svg fill="#e9b531" height="24" width="24" viewBox="0 0 24 24" enable-background="new 0 0 24 24"><path d="M22,9.24l-7.19-0.62L12,2L9.19,8.63L2,9.24l5.46,4.73L5.82,21L12,17.27L18.18,21l-1.63-7.03L22,9.24z M12,15.4V6.1 l1.71,4.04l4.38,0.38l-3.32,2.88l1,4.28L12,15.4z"/></svg>';
                    }

                    return '<svg fill="#e9b531" height="24" width="24" viewBox="0 0 24 24"><path d="M22 9.24l-7.19-.62L12 2 9.19 8.63L2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24zM12 15.4l-3.76 2.27 1-4.28-3.32-2.88 4.38-.38L12 6.1l1.71 4.04 4.38.38-3.32 2.88 1 4.28L12 15.4z"/></svg>';
                })
                ->toArray();

            return "<span title='$value' class='inline-flex items-center relative'>" . implode(' ', $stars) . '</span>';
        })(),

        'boolean' => (function () use ($value, $meta) {
            return "<span class='text-xl'>" . ($value ? '✅' : '❌') . '</span>';
        })(),

        'color' => (function () use ($value) {
            return "<div class='inline-block rounded-md w-6 h-6 border border-stroke' style='background: $value; border-color: $value;'>&nbsp;</div>";
        })(),

        'location' => (function () use ($value, $meta) {
            $val = json_decode($value) ?? '';
            if (!$val->coords ?? null) {
                return $value;
            }

            [$lng, $lat] = $val->coords;

            $val = "https://www.google.com/maps/search/?api=1&query=$lat,$lng";

            return "<a class='text-blue-500' target='_blank' href='$val'>View Location</a>";
        })(),

        'long text', 'string', 'link', 'file' => (function () use ($value, $meta) {
            return mb_strimwidth(strip_tags($value ?? ''), 0, 100, '...');
        })(),

        default => (function () use ($value, $type) {
            if (gettype($value) == 'object') {
                $value = json_encode($value);
            }

            if ($type == 'number') {
                $value = number_format($value);
            }

            return $value;
        })(),
    };
@endphp

<td class="p-3 text-sm max-w-[120px] truncate {{ $centered ? 'text-center' : 'text-left' }}">
    <span title="{{ $tooltip }}" class="{{ $loop->index == 0 ? 'pl-4' : '' }}">
        {!! $value !!}
    </span>
</td>

{{-- <tr data-v-627a034d="" class="text-capitalize">
    <td class="pier-td image undefined">
        <img src="https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?crop=entropy&amp;cs=tinysrgb&amp;fit=max&amp;fm=jpg&amp;ixid=MnwxNjE2NXwwfDF8c2VhcmNofDF8fGFwYXJ0bWVudHxlbnwwfHx8fDE2NTY3NDk0MjI&amp;ixlib=rb-1.2.1&amp;q=80&amp;w=1080"
            class="pier-col-image" />
    </td>

    <td class="pier-td string">
        <span>5 bedroom suite in m...</span>
    </td>

    <td class="pier-td number undefined">
        <span>1,250,000</span>
    </td>

    <td class="pier-td status undefined">
        <span class="inline-block rounded-full px-2 py-1.5 text-white text-xs leading-none font-medium"
            style="background: rgb(255, 152, 0);">
            Standard
        </span>
    </td>

    <td class="pier-td reference string">
        <span>Affordable apartment...
        </span>
    </td>

    <td class="pier-td long text undefined">
        <span></span>
    </td>

    <td class="text-center">
        <span class="inline-flex items-center justify-center">
            <a href="#" class="border-none bg-transparent border-orange-500 text-gray-600 mr-2 py-1 px-2">
                <svg fill="currentColor" height="20" width="20" viewBox="0 0 24 24">
                    <path
                        d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                </svg>
            </a>
            <a href="#" class="border-none bg-transparent text-orange-400 mr-2 py-1 px-2">
                <svg fill="currentColor" height="18" width="18" viewBox="0 0 24 24">
                    <path
                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                </svg>
            </a>

            <a href="#" class="border-none bg-transparent text-red-500 py-1 px-2">
                <svg fill="currentColor" height="20" width="20" viewBox="0 0 24 24">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM8 9h8v10H8V9zm7.5-5l-1-1h-5l-1 1H5v2h14V4z" />
                </svg>
            </a>
        </span>
    </td>
</tr> --}}
