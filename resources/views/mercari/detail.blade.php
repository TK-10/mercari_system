@extends('layouts.app')

@section('title', '商品詳細')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">商品詳細</h3>
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
            <form class="forms-sample" action="{{ url('/editComplet') }}" method="POST">
                @csrf
                <table class="table table-bordered">
                    <thead>
                        <tr align="center" bgcolor="#FFFFFF">
                            <th>商品ID</th>
                            <th>商品名</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($itemData))
                            <tr align="center" bgcolor="#FFFFFF">
                                <td>{{ $itemData->id }}</td>
                                <td>{{ $itemData->item_name }}</td>
                                <td style="width: 300px;">{{ $itemData->detail }}</td>

                                {{-- hidden input --}}
                                <input type="hidden" name="item_id" value="{{ $itemData->id }}">
                            </tr>
                        @endif
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
@endsection
