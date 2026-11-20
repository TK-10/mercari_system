@extends('layouts.app')

@section('title', '在庫数量追加')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">在庫数量追加</h3>
        </div>

        {{-- バリデーションエラー --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <br><br>

        <div id="contents">
            <form class="forms-sample" action="{{ url('/addStockComplet') }}" method="POST">
                @csrf
                <table class="table table-bordered">
                    <thead>
                        <tr align="center" bgcolor="#FFFFFF">
                            <th>商品ID</th>
                            <th>商品名</th>
                            <th>在庫数</th>
                            <th>在庫追加数</th>
                            <th>トータル数</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($itemData))
                            <tr bgcolor="#FFFFFF" align="center">
                                <td>{{ $itemData->id }}</td>
                                <td>{{ $itemData->item_name }}</td>
                                <td>{{ $itemData->stock }}</td>
                                <td>
                                    <input 
                                        type="number" 
                                        maxlength="20" 
                                        step="1" 
                                        placeholder="数値を入力" 
                                        style="text-align: center;" 
                                        name="item_stock" 
                                        v-model.number="addStock"
                                    >
                                </td>
                                <td>
                                    <b><span v-text="sum"></span></b>
                                </td>

                                {{-- hidden inputs --}}
                                <input type="hidden" name="item_id" value="{{ $itemData->item_id ?? $itemData->id }}">
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div style="text-align: right; margin-top: 30px;">
                    <button type="submit" class="btn btn-gradient-dark">送信</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Vue.js --}}
<script>
new Vue({
    el: "#contents",
    data: {
        data: @json($itemData->stock ?? 0),
        addStock: ''
    },
    computed: {
        sum() {
            return this.data + Number(this.addStock || 0);
        }
    }
});
</script>

{{-- input[type=number]のスピンボタン削除 --}}
<style>
input[type="number"]::-webkit-outer-spin-button, 
input[type="number"]::-webkit-inner-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
} 
input[type="number"] { 
    -moz-appearance:textfield; 
} 
</style>
@endsection