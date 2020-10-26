@extends('../layouts.panel', ['title' => 'Estadísticas'] )
@section('content')
    <h3>Estadísticas</h3>
    <iframe src="{{route('analytics_iframe')}}" frameborder="0" width="100%" height="800px"></iframe>
@endsection