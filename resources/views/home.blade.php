<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Weather</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <style>
    #map {
      height: 400px;
    }
    .main{
        width: 100%;
    }
    .history{
        width: auto;
    }
    .info{
        width: 100%;
        padding: 20px;
        margin-left: 10px;
    }
    .center{
        text-align: center;
    }
  </style>
</head>
<body>
   
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <a class="navbar-brand" href="#">JVN</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="{{ route('home') }}">Inicio</a></a>
              </li>
            </ul>
            
          </div>
        </div>
      </nav>
    <div class="container">
        <div class="mt-3">
            <form action="{{ route('search') }}" method="post">
                @csrf
                <select name="city" class="form-select" aria-label="Default select example">
                    <option selected value="">Seleccione una ciudad</option>
                    <option value="miami">Miami</option>
                    <option value="orlando">Orlando</option>
                    <option value="new york">New York</option>
                  </select>
                  <button type="submit" class="btn btn-success mt-2">Buscar</button>
            </form>   
            @if (isset($msg))
                <div class="alert alert-danger" role="alert">
                    {{$msg}}
                </div>
            @endif 
        </div>
      
           
        <div class="d-flex mt-4 main">
            <div class="bg-dark history center">
                <h1 class="text-light">Historial</h1>
                @foreach ($history as $history_item)
                    <ul class="list-group">
                        <li class="list-group-item">
                            <a href="{{route('history',['id'=>$history_item->id])}}" style="text-decoration: none; color:gray">
                                {{$history_item->name_city}} - {{$history_item->created_at}}
                            </a>
                        </li>
                    </ul>
                @endforeach
            </div>

            
            <div class="bg-light ml-5 info">
                @if (isset($status) && isset($data))
                    <h1 class="center">Informacion</h1>
                    <h2 class="center">Ciudad: {{$data->name}}</h2>
                    <h2>Condiciones Climaticas Actuales</h2>
                    <p><strong>Temperatura: </strong>{{$data->main->temp}}</p>
                    <p><strong>Sensacion termica: </strong>{{$data->main->feels_like}}</p>
                    <p><strong>Temperatura minima: </strong>{{$data->main->temp_min}}</p>
                    <p><strong>Temperatura maxima: </strong>{{$data->main->temp_max}}</p>
                    <p><strong>Presion: </strong>{{$data->main->pressure}}</p>
                    <p><strong>Humedad: </strong>{{$data->main->humidity}}</p>

                    <h2>Ubicacion</h2>
                    <p><strong>Longitud: </strong>{{$data->coord->lon}}</p>
                    <p><strong>Latitud: </strong>{{$data->coord->lat}}</p>
                    <div id="map"></div>

                @elseif(isset($history_detail) && !@empty($history_detail))
                    <h1 class="center">Informacion</h1>
                    <h2 class="center">Ciudad: {{$history_detail->name_city}}</h2>
                    <h2>Condiciones Climaticas A la Fecha: {{$history_detail->created_at}}</h2>
                    <p><strong>Temperatura: </strong>{{$history_detail->temp}}</p>
                    <p><strong>Sensacion termica: </strong>{{$history_detail->feels_like}}</p>
                    <p><strong>Temperatura minima: </strong>{{$history_detail->temp_min}}</p>
                    <p><strong>Temperatura maxima: </strong>{{$history_detail->temp_max}}</p>
                    <p><strong>Presion: </strong>{{$history_detail->pressure}}</p>
                    <p><strong>Humedad: </strong>{{$history_detail->humidity}}</p>

                    <h2>Ubicacion</h2>
                    <p><strong>Longitud: </strong>{{$history_detail->longitude}}</p>
                    <p><strong>Latitud: </strong>{{$history_detail->latitude}}</p>
                    <div id="map"></div> 
                        <?php
                        $data= json_decode('{
                                                "coord": {
                                                    "lon": '.$history_detail->longitude.',
                                                    "lat": '.$history_detail->latitude.'
                                                }
                                            }', false);
                            
                       ?>
                    
                @else
                    <?php
                    $data= json_decode('{
                                            "coord": {
                                                "lon": 0,
                                                "lat": 0
                                            }
                                        }', false);
                        
                   ?>
                   @endif
                </div>
            </div>  
    </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    // Obtener la ubicación desde PHP
    var lat = '<?= $data->coord->lat; ?>';
    var lon = '<?= $data->coord->lon; ?>';
    
    // Crear un mapa y establecer la ubicación inicial
    var map = L.map('map').setView([lat, lon], 13);

    // Agregar el proveedor de mapas (por ejemplo, Mapbox)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Agregar un marcador en la ubicación
    L.marker([lat, lon]).addTo(map);
  </script>  
</body>
</html>