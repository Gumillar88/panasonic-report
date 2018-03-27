@include('layouts.header', ['title' => 'News - Admin Panel'])
@include('layouts.navbar')

<div id="content" class="page">

    <h1>News</h1>

    @if(Session::has('news-created'))
        <div class="note success">Article has been created.</div>
    @endif

    @if(Session::has('news-removed'))
        <div class="note error">Article has been removed.</div>
    @endif

    <div>
        <a href="{{ env('APP_HOME_URL') }}/news/create" class="btn btn-green">Create</a>
    </div>

    <table id="newsTable" class="display initTable">
        <thead>
            <tr>
                <th data-width="5%">No.</th>
                <th data-width="70%">Title</th>
                <th data-width="25%">Options</th>
            </tr>
        </thead>
        <tbody>
            @foreach($news as $key => $article)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $article->title }}</td>
                    <td>
                        <a href="{{ env('APP_HOME_URL') }}/news/edit?ID={{ $article->ID }}" class="btn btn-green">Edit</a>
                        <a href="{{ env('APP_HOME_URL') }}/news/remove?ID={{ $article->ID }}" class="btn btn-red">Remove</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@include('layouts.footer');
