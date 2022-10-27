@php
    use Carbon\Carbon;
    $no = 1;
@endphp
<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Rain Rate (mm)</th>
        <th>Waktu</th>

    </tr>
    </thead>
    <tbody>
    @foreach($datas as $data)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $data->rain_rate_hi_mm == 0 ? "0" : $weatherHistory->rain_rate_hi_mm }}</td>
            <td>{{ Carbon::createFromTimestamp($data->unix_epoch_time)->format('d/m/Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
