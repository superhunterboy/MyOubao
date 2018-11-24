@foreach ($buttons['itemButtons'] as $element)
    <?php
    if (!$element->isAvailable($data)) continue;
    $target = $element->new_window ? '_blank' : '_self';
    ?>
    @if ($element->btn_type == 1)
    <a href="javascript:void(0)" class="btn btn-xs btn-embossed btn-danger" target="{{ $target }}"
     onclick="{{ $element->btn_action }}('{{ $element->route_name ? route($element->route_name, $data->id) : 'javascript:void(0);'  }}')">{{ __( $element->label) }}</a>
    @elseif ($element->btn_type == 2)
    <a href="{{ $element->route_name ? route($element->route_name, $data->id) : 'javascript:void(0);'}}" target="{{ $target }}" class="btn btn-xs btn-embossed btn-success">{{ __( $element->label) }}</a>
    @else
    <a  href="{{ $element->route_name ? route($element->route_name, [$element->para_name => $data->id]) : 'javascript:void(0);'
        }}" class="btn btn-xs btn-embossed btn-default" target="{{ $target }}" > {{ __( $element->label) }}</a>
    @endif
@endforeach
