@extends('layouts.app')

@section('title', '商品情報')

@section('content')

{{-- partial --}}
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title"> 商品情報 </h3>
        </div>

        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    {{-- 検索処理 start--}}
                    <form class="mercari-search" action="{{url('/item')}}">
                        <div class="search">
                            <h4 class="card-title">商品一覧</h4>
                            <input type="text" name="shouhin_id" value="" placeholder="商品ID">
                            <input type="text" name="shouhin_name" value="" placeholder="商品名">
                            <input type="text" name="item_price" value="" placeholder="商品価格">
                            <input type="text" name="item_stock" value="" placeholder="在庫数">
                            <div class="search_btn">
                                <button type="submit" class="btn btn-gradient-dark" value="">検索</button>
                            </div>
                        </div>
                    </form>
                    {{-- 検索処理 end--}}
                    <br>
                    <br>
                    <div id="contents">
                        {{-- tb01クラス追加 レスポンシブ対応のため--}}
                        <table class="table table-bordered tbl-r05">
                            <thead>
                                {{-- headクラス追加 レスポンシブ対応のため--}}
                                <tr class="head" align="center">
                                    <th> ID </th>
                                    <th> 商品名 </th>
                                    <th> 価格 </th>
                                    <th> 在庫数 </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- フォーム検索結果 --}}
                                {{-- data-label追加 レスポンシブ対応のため--}}

                                @foreach ($searchs as $search)
                                <tr bgcolor="#FFFFFF" align="center">
                                    <td data-label="id">{{ $search->id }}</td>
                                    <td data-label="商品名">{{ $search->item_name }}</td>
                                    <td data-label="商品価格">{{ $search->item_price }}円</td>
                                    <td data-label="在庫数">{{ $search->stock }}</td>
                                    <td data-label="履歴"><a href="{{url('/history', $search->item_id ?? $search->id)}}">履歴</a></td>
                                    <td data-label="詳細" align="center"><a href="{{url('/detail', $search->item_id ?? $search->id)}}">詳細</a></td>
                                    <td data-label="編集"><a href="{{url('/edit', $search->item_id ?? $search->id)}}">編集</a></td>
                                    <td data-label="数量追加"><a href="{{url('/stockAdd', $search->item_id ?? $search->id)}}">数量追加</a></td>
                                    <td data-label="削除">
                                        <form action="{{ route('item.delete', $search->item_id ?? $search->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('商品「' + {{ json_encode($search->item_name) }} + '」を削除しますか？');">
                                            @csrf
                                            <button type="submit" style="background:none; border:none; padding:0; cursor:pointer;" title="削除">
                                                <font color="#ff6347"><i class="mdi mdi-delete"></i></font>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="paginate">
                            {{ $searchs->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection