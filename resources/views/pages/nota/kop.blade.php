<table style="width: 100%; margin-bottom: 5px; border: 0;">
    <tr style="padding: 0;">
        <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
            @if ($company->image)
                <img src="{{ asset('storage/images/' . $company->image) }}" alt=""
                    width="70" height="70">
            @else
                <img src="{{ asset('img/default_img.jpg') }}" alt="" width="70"
                    height="70">
            @endif
        </td>
        <td style="width: 90%; border-bottom: 1px solid #000; padding: 5px;">
            <h1 style="text-align: center;">
                {{ $company->name }}
            </h1>
            <p style="padding: 0;text-align: center;">
                {{ $company->address }}
            </p>

            {{-- <p style="padding: 0;text-align: center;">
                {{ $company->about }}
            </p> --}}
        </td>
    </tr>
</table>
