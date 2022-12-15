@php
    use Carbon\Carbon;
    $no = 1;
@endphp
<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Rain Rate (mm)</th>
        <th>Rain Fall (mm)</th>
        <th>Time</th>

    </tr>
    </thead>
    <tbody>
    @foreach($datas as $data)
        <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $data->rain_rate_hi_mm == 0 ? "0" : $data->rain_rate_hi_mm }}</td>
            <td>{{ $data->rain_rate == 0 ? "0" : $data->rain_rate }}</td>
            <td>{{ Carbon::createFromTimestamp($data->unix_epoch_time)->format('d/m/Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
