@extends('layouts.app')

@section('title', '商品編集')

@section('content')

  {{-- partial --}}
  <div class="main-panel">
    <div class="content-wrapper">
      <div class="page-header">
        <h3 class="page-title"> 商品編集 </h3>
      </div>

      {{-- バリデーション エラー文 --}}
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      {{-- バリデーション エラー文  --}}

      <br><br>

      <div id="contents">
        <form class="forms-sample" action="{{ url('/editComplet') }}" method="POST">
          {{ csrf_field() }}
          <table class="table table-bordered">
            <thead>
              <tr align="center" bgcolor="#FFFFFF">
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
                  <td align="center">{{ $itemData->id }}</td>
                  <td align="center"><input type="text" style="text-align: center;" name="shouhin_name" value="{{ $item_name_tr }}"></td>
                  <td align="center"><input type="text" style="text-align: center;" name="item_price" value="{{ $itemData->item_price }}"></td>
                  <td align="center"><input type="text" style="text-align: center;" name="item_stock" value="{{ $itemData->stock }}"></td>
                  <td align="center">
                    <textarea name="item_detail" style="padding: 5px;">{{ $itemData->detail }}</textarea>
                  </td>
                  <input type="hidden" name="item_id" value="{{ $itemData->item_id ?? $itemData->id }}">
                </tr>
              @endif
            </tbody>
          </table>

          <div style="position:relative; left:930px; top:30px;">
            <button type="submit" class="btn btn-gradient-dark">送信</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  {{-- partial end --}}
@endsection