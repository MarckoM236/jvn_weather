<div class="card">
    <div class="card-header">
      <h1 class="center">Informacion</h1>
      <h2 class="center">Ciudad: {{$name}}</h2>
      @isset($weather->format_created)
        <h2>Condiciones Climaticas A la Fecha: {{$weather->format_created}}</h2>
      @endisset
    </div>
    <div class="card-body">
      <h2>Condiciones Climaticas Actuales</h2>
      <p><strong>Temperatura: </strong>{{$weather->temp - 273.15}} °C</p>
      <p><strong>Sensacion termica: </strong>{{$weather->feels_like - 273.15}} °C</p>
      <p><strong>Temperatura minima: </strong>{{$weather->temp_min - 273.15}} °C</p>
      <p><strong>Temperatura maxima: </strong>{{$weather->temp_max - 273.15}} °C</p>
      <p><strong>Presion: </strong>{{$weather->pressure}} PSI</p>
      <p><strong>Humedad: </strong>{{$weather->humidity}}%</p>
    </div>
  </div>

  <div class="card my-2">
    <div class="card-header">
      <h2>Ubicacion</h2>
    </div>
    <div class="card-body">
      <p><strong>Longitud: </strong>{{$coord->lon}}</p>
      <p><strong>Latitud: </strong>{{$coord->lat}}</p>
    </div>
  </div>