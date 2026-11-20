@extends('layouts.app')

@section('title', '商品登録')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div id="contents">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">商品登録</h4>

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

                        {{-- 登録完了メッセージ --}}
                        @if(isset($insert_ck))
                          <p style="color:red"><b>商品を登録しました</b></p>
                          <br>
                        @endif

                        <form class="forms-sample" action="{{ url('/addComplet') }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label for="exampleInputName1">商品名 (必須)</label>
                                <input type="text" name="item_name" class="form-control" value="{{ old('item_name') }}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputEmail3">商品価格 (必須)</label>
                                <input type="text" name="item_price" class="form-control"
                                    value="{{ old('item_price') }}">
                            </div>

                            <div class="form-group">
                                <label for="exampleInputPassword4">在庫数 (必須)</label>
                                <input type="text" name="item_stock" class="form-control"
                                    value="{{ old('item_stock') }}">
                            </div>

                            <div class="form-group">
                                <label for="exampleTextarea1">詳細</label>
                                <textarea class="form-control" id="exampleTextarea1"
                                    name="item_detail">{{ old('item_detail') }}</textarea>
                            </div>

                            <input type="hidden" name="redirect" value="{{ session('redirect') }}">

                            <button type="submit" class="btn btn-gradient-primary mr-2">登録</button>
                            {{-- <button class="btn btn-light">キャンセル</button> --}}

                            {{-- 類似商品モーダル --}}
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel">
                                <div class="modal-dialog modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                【重複登録の確認】類似商品があります。下記商品と重複がなければOKを押してください
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="text-muted">類似商品</p>
                                            @if (session('item'))
                                            @foreach (session('item') as $itm)
                                            <p class="px-2" id="modalBody">{{ $itm }}</p>
                                            @endforeach
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">閉じる</button>
                                            <button type="submit" class="btn btn-primary">OK</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 類似商品モーダル表示 --}}
<script>
    window.onload = function () {
        var rdt = @json(session('redirect'));
        if (rdt !== null) {
            $('#exampleModal').modal('show');
        }
    }
</script>

<style>
    #modalBody {
        white-space: pre-wrap;
    }
</style>
@endsection