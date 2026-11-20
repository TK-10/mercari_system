@extends('layouts.app')

@section('title', '商品別売上チャート')

@section('content')
<div class="main-panel">
	<div class="content-wrapper">
		<div class="page-header">
			<h3 class="page-title">mercari 商品別売上チャート</h3>
		</div>
		{{-- グーグルチャート用 --}}
		<div id="chart" style="height:500px; width:800px;"></div>
	</div>
</div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script>
        google.charts.load('current', { packages: ['corechart'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // PHPからJS配列に変換
            var chartData = [
                ['商品名', '販売数'],
                @foreach ($items as $item)
                    [{!! json_encode($item->item_name, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!}, {{ (int)$item->sold_count }}],
                @endforeach
            ];

            var data = google.visualization.arrayToDataTable(chartData);

            var options = {
                title: '商品別売上ランキング',
                is3D: true,
            };

            var chart = new google.visualization.PieChart(document.getElementById('chart'));
            chart.draw(data, options);
        }
    </script>

@endsection