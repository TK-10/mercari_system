@extends('layouts.app')

@section('title', '注文情報')

@section('content')

{{-- partial --}}
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> 注文情報 </h3>
        </div>

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- 検索処理 start--}}
                    <form class="mercari-search" action="{{url('/mercari')}}">
                        <h4 class="card-title">注文一覧</h4>
                        <input type="text" name="shouhin_id" value="" placeholder="商品ID">
                        <input type="text" name="shouhin_name" value="" placeholder="商品名">
                        <input type="text" name="buyer_name" value="" placeholder="購入者名">
                        <div style="position:relative;left: 750px;top :20px;">
                            <button type="submit" class="btn btn-gradient-dark" value="">検索</button>
                        </div>
                    </form>
                    {{-- 検索処理 end--}}
                    <br>
                    <br>
                    <div id="contents">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th> 商品ID </th>
                                    <th> 出品ID </th>
                                    <th> 商品名 </th>
                                    <th> 商品価格 </th>
                                    <th> 出品日 </th>
                                    <th> 購入者名 </th>
                                    <th> 在庫数 </th>
                                    <th> 出品サイト </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- bootstrap-datepickerのjavascriptコード --}}
<script>
    $('#date_sample').datepicker();
</script>
@endsection