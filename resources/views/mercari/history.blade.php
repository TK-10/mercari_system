@extends('layouts.app')

@section('title', '商品履歴')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">商品履歴 (在庫数 {{ $stock->stock }})</h3>
        </div>

        <div id="contents">
            <table class="table table-bordered text-center">
                <thead>
                    <tr bgcolor="#FFFFFF">
                        <th>ステータス</th>
                        <th>サイト名</th>
                        <th>商品名</th>
                        <th>商品価格</th>
                        <th>残り在庫</th>
                        <th>日付</th>
                        <th>ユーザー</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($history))
                        @foreach($history as $historys)
                            <tr bgcolor="#FFFFFF">
                                <td>
                                    {{-- ステータス振り分け --}}
                                    @switch($historys->status)
                                        @case(0) 購入 @break
                                        @case(1) 返品 @break
                                        @case(2) 新規登録 @break
                                        @case(3) 編集 @break
                                        @case(4) 在庫数追加 @break
                                        @default 削除
                                    @endswitch
                                </td>
                                <td>{{ $historys->site_name }}</td>
                                <td>{{ $historys->item_name }}</td>
                                <td>{{ $historys->item_price }}</td>
                                <td>{{ $historys->stock }}</td>
                                <td>{{ $historys->created_at }}</td>
                                <td>{{ $historys->user_name }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
