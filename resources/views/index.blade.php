@extends('layout.common')
@include('layout.header')

@section('content')
    <div class="row justify-content-center">
        <div class="col">
    <h1 class="h3">Wisdom Guildからカード情報を取得する</h1>
            <table class="table table-striped table-bordered mt-3">
                <thead class="">
                    <tr>
                        <th scope="col">カード番号</th>
                        <th scope="col">カード名</th>
                        <th scope="col">英語名</th>
                        <th scope="col">URL</th>
                    </tr>
                </thead>
                <tr>
                    <td>1</td>
                    <td>{{$card->getName()}}</td>
                    <td>{{$card->getEnname()}}</td>
                    <td>{{$card->getUrl()}}</td>
                </tr>
            </table>
        </div>
    </div>
@endsection
@include('layout.footer')