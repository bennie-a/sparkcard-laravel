@extends('layout.common')
@include('layout.header')

@section('content')
    <div class="row">
        <div class="col-6">
         <h1 class="h3">Wisdom Guildからカード情報を取得する</h1>
            <table class="table table-striped table-bordered mt-3">
                <thead class="">
                    <tr>
                        <th scope="col">カード番号</th>
                        <th scope="col">カード名</th>
                        <th scope="col">英語名</th>
                        <th scope="col">価格</th>
                    </tr>
                </thead>
                @foreach ($cardlist as $index => $card)
                <tr>
                    <td>{{$card->getIndex()}}</td>
                    <td>{{$card->getName()}}</td>
                    <td>{{$card->getEnname()}}</td>
                    <td>{{$card->getPrice()}}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection
@include('layout.footer')