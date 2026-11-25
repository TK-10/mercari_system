@extends('layouts.app')

@section('title', '注文管理リスト')

@section('content')

{{-- partial --}}
<div class="main-panel">
    <div class="content-wrapper">

        <div class="page-header">
            <h3 class="page-title"> mercari 注文管理リスト </h3>
        </div>

        {{-- コンテンツ移動 --}}
        <div id="contents">
            <div class="col-lg-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="search">
                            {{-- 検索処理 start--}}
                            <form class="mercari-search" action="{{url('/mercariOrder')}}">
                                <h4 class="card-title">注文一覧</h4>
                                <input type="text" name="shouhin_id" value="" placeholder="商品ID">
                                <input type="text" name="shouhin_name" value="" placeholder="商品名">
                                <input type="text" name="buyer_name" value="" placeholder="購入者名">
                                <label class="date-label">
                                    <input type="date" name="buy_date" id="buy_date" class="date">
                                </label>
                                <div class="search_btn">
                                    <button type="submit" class="btn btn-gradient-dark" value=""
                                        v-on:click="change('D')" v-bind:class="{'active': isActive === 'D'}">検索</button>
                                </div>
                            </form>
                        </div>
                        {{-- 検索処理 end--}}

                        <div class="tab_box">
                            <button type="button" class="btn btn-outline-danger btn-fw" v-on:click="change('A')"
                                v-bind:class="{'active': isActive === 'A'}">未到着</button>
                            <button type="button" class="btn btn-outline-info btn-fw" v-on:click="change('B')"
                                v-bind:class="{'active': isActive === 'B'}">到着済み</button>
                            <button type="button" class="btn btn-outline-dark btn-fw" v-on:click="change('C')"
                                v-bind:class="{'active': isActive === 'C'}">返品</button>
                            <br>
                            <br>
                        </div>
                        {{-- tb01クラス追加 レスポンシブ対応のため--}}
                        <table class="table table-bordered tbl-r05">
                            <thead>
                                <tr align="center">
                                    <th> 商品ID </th>
                                    <th> 商品名 </th>
                                    <th> 価格 </th>
                                    <th> 購入日 </th>
                                    <th> 購入者名 </th>
                                    <th> 処理区分 </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- フォーム検索結果 --}}
                                @foreach ($searchs as $search)
                                {{-- 未発送・発送済み・返品タグが選択されている時以外 --}}
                                <tr bgcolor="#FFFFFF" v-if="isActive !== 'A' && isActive !== 'B' && isActive !== 'C'">
                                    <td align="center" data-label="mid">{{ $search->mid }}</td>
                                    <td align="center" data-label="商品名">{{ $search->item_name }}</td>
                                    <td align="center" data-label="価格">{{ $search->item_price }}円</td>
                                    <td align="center" data-label="購入日">{{ $search->created_at }}</td>
                                    <td align="center" data-label="購入者名">{{ $search->buyer_name }}</td>
                                    <td align="center" data-label="処理区分">
                                        {{ ($search->shorikubun == 1 ? "未到着" : ($search->shorikubun == 2 ? "到着済み" : "返品"))}}
                                    </td>
                                </tr>
                                @endforeach

                                {{-- 未発送一覧 --}}
                                @foreach ($mercaris_mihassou as $mercari_m)
                                <tr bgcolor="#FFFFFF" v-if="isActive === 'A'">
                                    <td align="center">{{ $mercari_m->mid }}</td>
                                    <td align="center">{{ $mercari_m->item_name }}</td>
                                    <td align="center">{{ $mercari_m->item_price }}円</td>
                                    <td align="center">{{ $mercari_m->created_at }}</td>
                                    <td align="center">{{ $mercari_m->buyer_name }}</td>
                                    <td align="center">
                                        {{ ($mercari_m->shorikubun == 1 ? "未到着" : ($mercari_m->shorikubun == 2 ? "到着済み" : "返品"))}}
                                    </td>
                                </tr>
                                @endforeach

                                {{-- 発送済一覧 --}}
                                @foreach ($mercaris_hassouzumi as $mercari_z)
                                <tr bgcolor="#FFFFFF" v-if="isActive === 'B'">
                                    <td align="center">{{ $mercari_z->mid }}</td>
                                    <td align="center">{{ $mercari_z->item_name }}</td>
                                    <td align="center">{{ $mercari_z->item_price }}円</td>
                                    <td align="center">{{ $mercari_z->created_at }}</td>
                                    <td align="center">{{ $mercari_z->buyer_name }}</td>
                                    <td align="center">
                                        {{ ($mercari_z->shorikubun == 1 ? "未到着" : ($mercari_z->shorikubun == 2 ? "到着済み" : "返品"))}}
                                    </td>
                                </tr>
                                @endforeach

                                {{-- 返品一覧 --}}
                                @foreach ($mercaris_henpin as $mercari_h)
                                <tr bgcolor="#FFFFFF" v-if="isActive === 'C'">
                                    <td align="center">{{ $mercari_h->mid }}</td>
                                    <td align="center">{{ $mercari_h->item_name }}</td>
                                    <td align="center">{{ $mercari_h->item_price }}円</td>
                                    <td align="center">{{ $mercari_h->created_at }}</td>
                                    <td align="center">{{ $mercari_h->buyer_name }}</td>
                                    <td align="center">
                                        {{ ($mercari_h->shorikubun == 1 ? "未到着" : ($mercari_h->shorikubun == 2 ? "到着済み" : "返品"))}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- 全ページタグ  --}}
                        <div class="paginate" v-if="isActive !== 'A' && isActive !== 'B' && isActive !== 'C'">
                            {{ $searchs->appends(request()->input())->links() }}
                        </div>
                        {{-- 未到着ページタグ  --}}
                        <div class="paginate" v-if="isActive === 'A'">
                            {{ $mercaris_mihassou->appends(request()->input())->links() }}
                        </div>
                        {{-- 到着済みページタグ  --}}
                        <div class="paginate" v-if="isActive === 'B'">
                            {{ $mercaris_hassouzumi->appends(request()->input())->links() }}
                        </div>
                        {{-- 返品ページタグ  --}}
                        <div class="paginate" v-if="isActive === 'C'">
                            {{ $mercaris_henpin->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    new Vue({
        el: '#contents',
        data: {
            isActive: ''
        },
        mounted() {
            if (localStorage.isActive) {
                this.isActive = localStorage.isActive;
            }
        },
        methods: {
            change: function (num) {
                this.isActive = num
                localStorage.setItem('isActive', num); /* changeメソッド(ボタン押された時)localStorageに値をセット */
            }

        }
    })
</script>

<script>
    const dateInput = document.getElementById('buy_date');
    const label = document.querySelector('.date-label');

    label.addEventListener('click', (e) => {
        e.preventDefault(); // デフォルトの動作防止
        dateInput.showPicker(); // カレンダーを開く！
    });
</script>
@endsection