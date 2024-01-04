@php
    $value = $row->{$field->label};
    $type = $field->type;
    $centered = $field->centered ?? false;
    $meta = $field->meta ?? null;

    if ($type == 'reference') {
        $type = $meta->type;
        $value = $value->{$meta->field};
    }

    $value = match ($type) {
        'image' => (function () use ($value, $meta) {
            $sizing = $meta && $meta->face ? ' w-[40px] aspect-square rounded-full' : ' w-[60px] aspect-[2/1.3] rounded';
            return "<img class='$sizing inline-block object-cover object-center' src='$value' />";
        })(),

        'date' => (function () use ($value) {
            $date = \Carbon\Carbon::parse($value);
            return $date->format($date->year === now()->year ? 'M d, g:i A' : 'M d, Y, g:i A');
        })(),

        'location' => (function () use ($value, $meta) {
            return "<img class='pier-col-image' src='' />";
        })(),

        'long text', 'string', 'link', 'file' => (function () use ($value, $meta) {
            return mb_strimwidth(strip_tags($value ?? ''), 0, 100, '...');
        })(),

        default => (function () use ($value) {
            if (gettype($value) == 'object') {
                $value = json_encode($value);
            }

            return $value;
        })(),
    };
@endphp

<td class="p-3 text-sm max-w-[120px] truncate {{ $centered ? 'text-center' : 'text-left' }}">
    {!! $value !!}
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
