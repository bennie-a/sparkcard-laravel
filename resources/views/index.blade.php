@extends('layout.common')
@include('layout.header')

@section('content')
    <div class="row justify-content-center">
        <div class="col-10">
            <table class="table table-striped table-dark mt-5">
                <tr>
                    <th>タイトル</th>
                    <th>いいね数</th>
                    <th>コメント数</th>
                    <th>作成日</th>
                </tr>
            </table>
        </div>
    </div>
@endsection
@include('layout.footer')