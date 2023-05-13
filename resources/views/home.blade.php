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
        border-radius: 2rem;
    }
    .info{
        width: 83%;
        padding: 20px;
        margin-left: 10px;
    }
    .center{
        text-align: center;
    }

    .card{
      border-radius: 2rem !important;
    }
    .card-header{
      border-radius: 2rem 2rem 0 0 !important;
    }

    #view-history{
      display: none;
    }
    .reset{
      background: white;
      border-radius: 3rem;
      padding: 0rem;
      margin-top: -1px;
    }

    @media(max-width: 768px){
      .main{
        flex-direction: column;
      }
      .history{
        width: 100%;
      }
      .info{
        width: 100%;
        margin-left: 0px;
      }

      #view-history{
        display: block;
        margin-left: auto;
        margin-right: auto;
        width: 50%;
      }

      .list-history{
        display: none; 
      }
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
              <div class="d-flex justify-content-center align-items-center my-2">
                <h1 class="text-light small">Historial</h1>
                <a href="{{route('clear')}}" class="btn btn-sm mx-2 reset">
                  <img src="{{asset('assets/icons/update_sync_reload_reset_icon_229478.png')}}" alt="Limpiar" title="Limpiar Historial" width="25px">
                </a>
              </div>
                <button type="button" class="btn btn-sm btn-warning mb-2" id="view-history">
                    Ver historial
                </button>
                <div class="list-history mx-2">
                  @foreach ($history as $history_item)
                      <ul class="list-group">
                          <li class="list-group-item mb-2">
                              <a href="{{route('history',['id'=>$history_item->id])}}" style="text-decoration: none; color:gray">
                                  {{$history_item->name_city}} <br>
                                  {{$history_item->created_at}}
                              </a>
                              <form action="{{route('delete', $history_item->id)}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm">
                                  <img src="{{asset('assets/icons/trash_bin_icon-icons.com_67981.png')}}" alt="Eliminar" title="Eliminar!" width="20px">
                                </button>
                              </form>
                          </li>
                      </ul>
                  @endforeach
                </div>
            </div>

            
            <div class="bg-light ml-5 info">
                @if (isset($status) && isset($data))
                    @include('component.card-info', ["name" => $data->name, "weather" => $data->main, "coord" => $data->coord])
                    <div id="map"></div>

                @elseif(isset($history_detail) && !@empty($history_detail))
                  @include('component.card-info', ["name" => $history_detail->name, "weather" => $history_detail->main, "coord" => $history_detail->coord])
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
  
  <script>
    /* ready function vanila js */
    document.addEventListener('DOMContentLoaded', function() {
        /* evento click */
        document.getElementById('view-history').addEventListener('click', function() {
            /* obtenemos el elemento */
            var element = document.querySelector('.list-history');
            /* verify if have display none */
            if (element.style.display == 'block') {
                element.style.display = 'none';
                this.innerHTML = 'Ver historial';
                return;
            }
            element.style.display = 'block';
            this.innerHTML = 'Ocultar historial';
        });
    });
  </script>
</body>
</html>