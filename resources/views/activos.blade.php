<table class="table">
  <thead>
    <tr>
      <th scope="col">Cedula o rif</th>
      <th scope="col">Nombre y apellido o social</th>
      <th scope="col">estatus</th>
      <th scope="col">Handle</th>
    </tr>
  </thead>
  <tbody>
    @foreach($lista as $lista)
    <tr>

      <td>{{$lista->kind.$lista->dni}}</td>
    </tr>
    @endforeach
    @foreach($lista1 as $lista1)
    <tr>
      <th scope="row">2</th>
    </tr>
    <td>
      <td>{{$lista1->nombre." ".$lista1->apellido}}</td>
    </td>
    <tr>
    @endforeach
      <th scope="row">3</th>
      <td>Larry</td>
      <td>the Bird</td>
      <td>@twitter</td>
    </tr>
  </tbody>
</table>
