<html>
<head>
    <title>Example of Loop 2</title>
    <!-- Google Fonts -->
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <!-- CSS Reset -->
    <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
    <!-- Milligram CSS minified -->
    <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
</head>
<body>
<h1>Example of Loops</h1>
@foreach ($users as $user)
    <hr>
    first:{{$loop->first}}<br>
    last:{{$loop->last}}<br>
    Name user:{{$user->name}}<br>
    <ul>
    @foreach ($user->posts as $post)
        <li>first:{{$loop->first}}<br>
            last:{{$loop->last}}<br>
            {{$loop->parent->index}}<br>
            {{$loop->index}}<br> {{$loop->even}}: Subject:{{$post->subject}}</li>

    @endforeach
    </ul>
@endforeach
</body>
</html>