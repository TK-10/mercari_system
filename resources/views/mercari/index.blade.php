@extends('layouts.app')

@section('title', 'メインページ')

@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <div class="container">
                <div class="row">

                    {{-- 商品情報 --}}
                    <div class="col-md-4">
                        <div class="main">
                            <a class="nav-link text-dark" href="{{ url('/item') }}">
                                <div class="service">
                                    <div class="service-logo">
                                        <img src="{{ asset('assets/images/item.png') }}" alt="商品情報">
                                    </div>
                                    <h4>商品情報</h4>
                                </div>
                            </a>
                            <div class="shadowOne"></div>
                            <div class="shadowTwo"></div>
                        </div>
                    </div>

                    {{-- 商品登録 --}}
                    <div class="col-md-4">
                        <div class="main">
                            <a class="nav-link text-dark" href="{{ url('/addItem') }}">
                                <div class="service">
                                    <div class="service-logo">
                                        <img src="{{ asset('assets/images/order.png') }}" alt="商品登録">
                                    </div>
                                    <h4>商品登録</h4>
                                </div>
                            </a>
                            <div class="shadowOne"></div>
                            <div class="shadowTwo"></div>
                        </div>
                    </div>

                    {{-- mercari 注文管理情報 --}}
                    <div class="col-md-4">
                        <div class="main">
                            <a class="nav-link text-dark" href="{{ url('/mercariOrder') }}">
                                <div class="service">
                                    <div class="service-logo">
                                        <img src="{{ asset('assets/images/mercari_Icon.png') }}" alt="メルカリ注文">
                                    </div>
                                    <h4>mercari<br>注文管理情報</h4>
                                </div>
                            </a>
                            <div class="shadowOne"></div>
                            <div class="shadowTwo"></div>
                        </div>
                    </div>

                    {{-- 月別売上チャート --}}
                    <div class="col-md-4">
                        <div class="main">
                            <a class="nav-link text-dark" href="{{ url('/chart') }}">
                                <div class="service">
                                    <div class="service-logo">
                                        <img src="{{ asset('assets/images/chart1.png') }}" alt="売上チャート">
                                    </div>
                                    <h4>月別売上<br>チャート</h4>
                                </div>
                            </a>
                            <div class="shadowOne"></div>
                            <div class="shadowTwo"></div>
                        </div>
                    </div>

                </div> {{-- /.row --}}
            </div> {{-- /.container --}}
        </div>
    </div>
</div>
@endsection