<table style="width: 100%; margin-bottom: 5px; border: 0;">
    <tr style="padding: 0;">
        <td style="width: 10%; border-bottom: 1px solid #000; padding: 5px;">
            <img src="{{ asset('storage/images/' . profileCompany()->image) }}" alt="" width="70" height="70"
                onerror="this.onerror=null; this.src='{{ asset('img/default_img.jpg') }}';">

        </td>
        <td style="width: 90%; border-bottom: 1px solid #000; padding: 5px;">
            <h1 style="text-align: center;">
                {{ profileCompany()->name }}
            </h1>
            <p style="padding: 0;text-align: center;">

                {{ profileCompany()->address }}
            </p>

            <p style="padding: 0;text-align: center;">
                {{ profileCompany()->email . ' ' . profileCompany()->phone }}
            </p>
        </td>
    </tr>
</table>
