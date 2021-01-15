@extends('mb.fit')


@section('table')
    <table border="1"> 
        @for ($i = 0; $i < $row; $i++)
        <tr>
            @for ($j = 0; $j < $col; $j++)
                <td><input size="5" value="{{$i+1}}x{{$j+1}}"></td>   
            @endfor
        </tr> 
        @endfor
    </table>
@endsection