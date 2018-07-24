@if ($comment->history->pluck('score')->count() > 0)
<div class="float-right">
    <canvas id="sparkline{{ $comment->id }}" width="100" height="25"></canvas>

    @push ('after-scripts')
    <script>
    new Chart(document.getElementById('sparkline{{ $comment->id }}').getContext('2d'), {
        type: 'line',
        data: {
            labels: [{{ $comment->history->pluck('score')->implode(',') }}],
            datasets: [
                {
                    data: [{{ $comment->history->pluck('score')->implode(',') }}]
                }
            ]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            elements: {
                line: {
                    borderColor: '#000000',
                    borderWidth: 1,
                    fill: false
                },
                point: {
                    radius: 0
                }
            },
            tooltips: {
                enabled: false
            },
            scales: {
                yAxes: [
                    {
                        display: false
                    }
                ],
                xAxes: [
                    {
                        display: false
                    }
                ]
            },
            animation: false
        }
    });
    </script>
    @endpush
</div>
@endif