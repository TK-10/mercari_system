@extends('layouts.app')

@section('title', '商品更新確認')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">商品更新確認</h3>
        </div>

        <div id="contents">
            {{-- 更新または数量追加メッセージ --}}
            @if($itemUpdate == 1)
                <p class="text-danger font-weight-bold">商品情報を更新しました</p>
            @endif

            <form class="forms-sample" action="{{ url('/editComplet') }}" method="POST">
                @csrf
                <table class="table table-bordered text-center">
                    <thead>
                        <tr bgcolor="#FFFFFF">
                            <th>商品ID</th>
                            <th>商品名</th>
                            <th>商品価格</th>
                            <th>在庫数</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($itemData))
                        <tr bgcolor="#FFFFFF">
                            <td>{{ $itemData->id }}</td>
                            <td>{{ $itemData->item_name }}</td>
                            <td>{{ $itemData->item_price }}</td>
                            <td>{{ $itemData->stock }}</td>
                            <td style="width: 300px;">{{ $itemData->detail }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
@endsection