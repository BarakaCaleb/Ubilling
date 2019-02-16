<?php

/*
 * Yandex maps API implementation
 */

/**
 * Shows map container
 *
 * @return void
 *  
 */
function sm_ShowMapContainer() {
    $container = wf_tag('div', false, '', 'id="ubmap" style="width: 1000; height:800px;"');
    $container.=wf_tag('div', true);
    $controls = wf_Link("?module=usersmap", wf_img('skins/ymaps/build.png') . ' ' . __('Builds map'), false, 'ubButton');
    $controls.= wf_Link("?module=switchmap", wf_img('skins/ymaps/network.png') . ' ' . __('Switches map'), false, 'ubButton');
    if (cfr('SWITCHESEDIT')) {
        $controls.= wf_Link("?module=switchmap&locfinder=true", wf_img('skins/ymaps/edit.png') . ' ' . __('Edit map'), false, 'ubButton');
    }
    $controls.= wf_Link("?module=switchmap&showuplinks=true", wf_img('skins/ymaps/uplinks.png') . ' ' . __('Show links'), false, 'ubButton');
    $controls.= wf_Link("?module=switchmap&clusterer=true", wf_img('skins/ymaps/cluster.png') . ' ' . __('Clusterer'), false, 'ubButton');
    $controls.= wf_Link("?module=switchmap&coverage=true", wf_img('skins/ymaps/coverage.png') . ' ' . __('Coverage area'), false, 'ubButton');
    if (cfr('SWITCHES')) {
        $controls.= wf_Link("?module=switches", wf_img('skins/ymaps/switchdir.png') . ' ' . __('Available switches'), true, 'ubButton');
    }
    $controls.=wf_delimiter(1);

    show_window(__('Active equipment map'), $controls . $container);
}

/**
 * Shows map container for builds
 *
 * @return void
 */
function um_ShowMapContainer() {
    $container = wf_tag('div', false, '', 'id="ubmap" style="width: 1000; height:800px;"');
    $container.=wf_tag('div', true);

    $controls = wf_Link("?module=switchmap", wf_img('skins/ymaps/network.png') . ' ' . __('Switches map'), false, 'ubButton');
    $controls.= wf_Link("?module=usersmap", wf_img('skins/ymaps/build.png') . ' ' . __('Builds map'), false, 'ubButton');
    $controls.= wf_Link("?module=usersmap&locfinder=true", wf_img('skins/ymaps/edit.png') . ' ' . __('Edit map'), false, 'ubButton');
    $controls.= wf_Link("?module=usersmap&clusterer=true", wf_img('skins/ymaps/cluster.png') . ' ' . __('Clusterer'), false, 'ubButton');



    $controls.=wf_delimiter(1);

    show_window(__('Builds and users map'), $controls . $container);
}

/**
 * Returns geo coordinates locator
 * 
 * @return string
 */
function sm_MapLocationFinder() {
    $title = wf_tag('b') . __('Place coordinates') . wf_tag('b', true);
    $data = sm_MapLocationSwitchForm();
    $result = generic_MapEditor('placecoords', $title, $data);
    return ($result);
}

/**
 * Return geo coordinates locator for builds
 * 
 * @return string
 */
function um_MapLocationFinder() {
    $title = wf_tag('b') . __('Place coordinates') . wf_tag('b', true);
    $data = um_MapLocationBuildForm();
    $result = generic_MapEditor('placecoords', $title, $data);
    return ($result);
}

/**
 * Initialize map container with some settings
 * 
 * @param $center - map center lat,long
 * @param $zoom - default map zoom
 * @param $type - map type, may be: map, satellite, hybrid
 * @param $placemarks - already filled map placemarks
 * @param $editor - field for visual editor or geolocator
 * @param $lang - map language in format ru-RU
 * 
 * @return void
 */
function sm_MapInit($center, $zoom, $type, $placemarks = '', $editor = '', $lang = 'ru-RU') {
    if (empty($center)) {
        $center = 'ymaps.geolocation.latitude, ymaps.geolocation.longitude';
    }

    if (wf_CheckGet(array('clusterer'))) {
        $clusterer = ',
 		clusterer = new ymaps.Clusterer({
            //preset: \'twirl#invertedVioletClusterIcons\',
            groupByCoordinates: false,
            clusterDisableClickZoom: true
        });

        clusterer.options.set({
        gridSize: 80,
        clusterDisableClickZoom: false
        });  myMap.geoObjects.add(clusterer); ';
    } else {
        $clusterer = ';';
    }

    $js = wf_tag('script', false, '', 'src="https://api-maps.yandex.ru/2.0/?load=package.full&lang=' . $lang . '"  type="text/javascript"');
    $js.= wf_tag('script', true);
    $js.= wf_tag('script', false, '', 'type="text/javascript"');
    $js.= '
        ymaps.ready(init);
    function init () {
            var myMap = new ymaps.Map(\'ubmap\', {
                    center: [' . $center . '], 
                    zoom: ' . $zoom . ',
                    type: \'yandex#' . $type . '\',
                    behaviors: [\'default\',\'scrollZoom\']
                })' . $clusterer . '
               
                   myMap.controls
                .add(\'zoomControl\')
                .add(\'typeSelector\')
                .add(\'mapTools\')
                .add(\'searchControl\');
               
         ' . $placemarks . '    
         ' . $editor . '
             
    }';
    $js.=wf_tag('script', true);

    show_window('', $js);
}

/**
 * Initialize map container with some settings
 * 
 * @param $center - map center lat,long
 * @param $zoom - default map zoom
 * @param $type - map type, may be: map, satellite, hybrid
 * @param $placemarks - already filled map placemarks
 * @param $editor - field for visual editor or geolocator
 * @param $lang - map language in format ru-RU
 * 
 * @return void
 */
function sm_MapInitQuiet($center, $zoom, $type, $placemarks = '', $editor = '', $lang = 'ru-RU') {
    if (empty($center)) {
        $center = 'ymaps.geolocation.latitude, ymaps.geolocation.longitude';
    }

    if (wf_CheckGet(array('clusterer'))) {
        $clusterer = ',
 		clusterer = new ymaps.Clusterer({
            //preset: \'twirl#invertedVioletClusterIcons\',
            groupByCoordinates: false,
            clusterDisableClickZoom: true
        });

        clusterer.options.set({
        gridSize: 80,
        clusterDisableClickZoom: false
        });  myMap.geoObjects.add(clusterer); ';
    } else {
        $clusterer = ';';
    }

    $js = wf_tag('script', false, '', 'src="https://api-maps.yandex.ru/2.0/?load=package.full&lang=' . $lang . '"  type="text/javascript"');
    $js.= wf_tag('script', true);
    $js.= wf_tag('script', false, '', 'type="text/javascript"');
    $js.= '
        ymaps.ready(init);
    function init () {
            var myMap = new ymaps.Map(\'ubmap\', {
                    center: [' . $center . '], 
                    zoom: ' . $zoom . ',
                    type: \'yandex#' . $type . '\',
                    behaviors: [\'default\',\'scrollZoom\']
                })' . $clusterer . '
               
                   myMap.controls
                .add(\'zoomControl\')
                .add(\'typeSelector\')
                .add(\'mapTools\')
                .add(\'searchControl\');
               
         ' . $placemarks . '    
         ' . $editor . '
             
    }';
    $js.= wf_tag('script', true);

    return ($js);
}

/**
 * Initialize map container with some settings
 * 
 * @param $center - map center lat,long
 * @param $zoom - default map zoom
 * @param $type - map type, may be: map, satellite, hybrid
 * @param $placemarks - already filled map placemarks
 * @param $editor - field for visual editor or geolocator
 * @param $lang - map language in format ru-RU
 * 
 * @return void
 */
function sm_MapInitBasic($center, $zoom, $type, $placemarks = '', $editor = '', $lang = 'ru-RU') {
    if (empty($center)) {
        $center = 'ymaps.geolocation.latitude, ymaps.geolocation.longitude';
    }

    if (wf_CheckGet(array('clusterer'))) {
        $clusterer = ',
 		clusterer = new ymaps.Clusterer({
            //preset: \'twirl#invertedVioletClusterIcons\',
            groupByCoordinates: false,
            clusterDisableClickZoom: true
        });

        clusterer.options.set({
        gridSize: 80,
        clusterDisableClickZoom: false
        });  myMap.geoObjects.add(clusterer); ';
    } else {
        $clusterer = ';';
    }


    $js = wf_tag('script', false, '', 'src="https://api-maps.yandex.ru/2.0/?load=package.full&lang=' . $lang . '"  type="text/javascript"');
    $js.= wf_tag('script', true);
    $js.= wf_tag('script', false, '', 'type="text/javascript"');
    $js.= '
        ymaps.ready(init);
    function init () {
            var myMap = new ymaps.Map(\'ubmap\', {
                    center: [' . $center . '], 
                    zoom: ' . $zoom . ',
                    type: \'yandex#' . $type . '\',
                    behaviors: [\'default\',\'scrollZoom\']
                })' . $clusterer . '
               
                   myMap.controls
                .add(\'zoomControl\')
                .add(\'typeSelector\')
                .add(\'searchControl\');
               
         ' . $placemarks . '    
         ' . $editor . '
             
    }';
    $js.= wf_tag('script', true);


    return($js);
}

/**
 * Returns map mark
 * 
 * @param $coords - map coordinates
 * @param $title - ballon title
 * @param $content - ballon content
 * @param $footer - ballon footer content
 * @param $icon - YM icon class
 * @param $iconlabel - icon label string
 * @param $canvas - is canvas rendering enabled?
 * 
 * @return string
 */
function sm_MapAddMark($coords, $title = '', $content = '', $footer = '', $icon = 'twirl#lightblueIcon', $iconlabel = '', $canvas = false) {
    if ($canvas) {
        if ($iconlabel == '') {
            $overlay = 'overlayFactory: "default#interactiveGraphics"';
        } else {
            $overlay = '';
        }
    } else {
        $overlay = '';
    }

    if (!wf_CheckGet(array('clusterer'))) {
        $markType = 'myMap.geoObjects';
    } else {
        $markType = 'clusterer';
    }

    $result = '
            myPlacemark = new ymaps.Placemark([' . $coords . '], {
                 iconContent: \'' . $iconlabel . '\',
                 balloonContentHeader: \'' . $title . '\',
                 balloonContentBody: \'' . $content . '\',
                 balloonContentFooter: \'' . $footer . '\',
                 hintContent: "' . $content . '",
                } , {
                    draggable: false,
                    preset: \'' . $icon . '\',
                    ' . $overlay . '
                        
                }),
 
            
           ' . $markType . '.add(myPlacemark);
        
            
            ';
    return ($result);
}

/**
 * Returns map circle
 * 
 * @param $coords - map coordinates
 * @param $radius - circle radius in meters
 * @param $canvas - is canvas rendering enabled?
 * 
 * @return string
 *  
 */
function sm_MapAddCircle($coords, $radius, $content = '', $hint = '') {


    $result = '
             myCircle = new ymaps.Circle([
                    [' . $coords . '],
                    ' . $radius . '
                ], {
                    balloonContent: "' . $content . '",
                    hintContent: "' . $hint . '"
                }, {
                    draggable: true,
             
                    fillColor: "#00a20b55",
                    strokeColor: "#006107",
                    strokeOpacity: 0.5,
                    strokeWidth: 1
                });
    
            myMap.geoObjects.add(myCircle);
            ';

    return ($result);
}

/**
 * Returns JS code to draw line within two points
 * 
 * @param string $coord1
 * @param string $coord2
 * @param string $color
 * @param string $hint
 * 
 * @return string
 */
function sm_MapAddLine($coord1, $coord2, $color = '', $hint = '', $width = '') {
    $hint = (!empty($hint)) ? 'hintContent: "' . $hint . '"' : '';
    $color = (!empty($color)) ? $color : '#000000';
    $width = (!empty($color)) ? $width : '1';

    $result = '
         var myPolyline = new ymaps.Polyline([[' . $coord1 . '],[' . $coord2 . ']], 
             {' . $hint . '}, 
             {
              draggable: false,
              strokeColor: \'' . $color . '\',
              strokeWidth: \'' . $width . '\'
             }
             
             );
             myMap.geoObjects.add(myPolyline);
            ';
    return ($result);
}

/**
 * Initalizes maps API with some params
 * 
 * @param string $center
 * @param int $zoom
 * @param string $type
 * @param string $placemarks
 * @param bool $editor
 * @param string $lang
 * @param string $container
 * 
 * @return string
 */
function generic_MapInit($center, $zoom, $type, $placemarks = '', $editor = '', $lang = 'ru-RU', $container = 'ubmap') {
    if (empty($center)) {
        $center = 'ymaps.geolocation.latitude, ymaps.geolocation.longitude';
    } else {
        $center = $center;
    }

    $result = wf_tag('script', false, '', 'src="https://api-maps.yandex.ru/2.0/?load=package.full&lang=' . $lang . '"  type="text/javascript"');
    $result.=wf_tag('script', true);
    $result.=wf_tag('script', false, '', 'type="text/javascript"');
    $result.= '
        ymaps.ready(init);
        function init () {
            var myMap = new ymaps.Map(\'custmap\', {
                    center: [' . $center . '], 
                    zoom: ' . $zoom . ',
                    type: \'yandex#' . $type . '\',
                    behaviors: [\'default\',\'scrollZoom\']
                })
               
                 myMap.controls
                .add(\'zoomControl\')
                .add(\'typeSelector\')
                .add(\'mapTools\')
                .add(\'searchControl\');
               
         ' . $placemarks . '    
         ' . $editor . '
    }';
    $result.=wf_tag('script', true);

    return ($result);
}

/**
 * Returns map circle
 * 
 * @param $coords - map coordinates
 * @param $radius - circle radius in meters
 * 
 * @return string
 *  
 */
function generic_MapAddCircle($coords, $radius, $content = '', $hint = '') {
    $result = '
             myCircle = new ymaps.Circle([
                    [' . $coords . '],
                    ' . $radius . '
                ], {
                    balloonContent: "' . $content . '",
                    hintContent: "' . $hint . '"
                }, {
                    draggable: true,
             
                    fillColor: "#00a20b55",
                    strokeColor: "#006107",
                    strokeOpacity: 0.5,
                    strokeWidth: 1
                });
    
            myMap.geoObjects.add(myCircle);
            ';

    return ($result);
}

/**
 * Returns map mark
 * 
 * @param string $coords - map coordinates
 * @param string $title - ballon title
 * @param string $content - ballon content
 * @param string $footer - ballon footer content
 * @param string $icon - YM icon class
 * @param string $iconlabel - icon label string
 * @param string $canvas
 * 
 * @return string
 */
function generic_mapAddMark($coords, $title = '', $content = '', $footer = '', $icon = 'twirl#lightblueIcon', $iconlabel = '', $canvas = '') {
    if ($canvas) {
        if ($iconlabel == '') {
            $overlay = 'overlayFactory: "default#interactiveGraphics"';
        } else {
            $overlay = '';
        }
    } else {
        $overlay = '';
    }

    if (!wf_CheckGet(array('clusterer'))) {
        $markType = 'myMap.geoObjects';
    } else {
        $markType = 'clusterer';
    }

    $result = '
            myPlacemark = new ymaps.Placemark([' . $coords . '], {
                 iconContent: \'' . $iconlabel . '\',
                 balloonContentHeader: \'' . $title . '\',
                 balloonContentBody: \'' . $content . '\',
                 balloonContentFooter: \'' . $footer . '\',
                 hintContent: "' . $content . '",
                } , {
                    draggable: false,
                    preset: \'' . $icon . '\',
                    ' . $overlay . '
                        
                }),
 
            
           ' . $markType . '.add(myPlacemark);
        
            
            ';
    return($result);
}

/**
 * Returns maps empty container
 * 
 * @return string
 */
function generic_MapContainer($width = '', $height = '', $id = '') {
    $width = (!empty($width)) ? $width : '100%';
    $height = (!empty($height)) ? $height : '800px;';
    $id = (!empty($id)) ? $id : 'ubmap';
    $result = wf_tag('div', false, '', 'id="' . $id . '" style="width: 100%; height:800px;"');
    $result.=wf_tag('div', true);
    return ($result);
}

/**
 * Returns JS code to draw line within two points
 * 
 * @param string $coord1
 * @param string $coord2
 * @param string $color
 * @param string $hint
 * 
 * @return string
 */
function generic_MapAddLine($coord1, $coord2, $color = '', $hint = '', $width = '') {
    return (sm_MapAddLine($coord1, $coord2, $color, $hint, $width));
}

/**
 * Return generic editor code
 * 
 * @param string $name
 * @param string $title
 * @param string $data
 * 
 * @return string
 */
function generic_MapEditor($name, $title = '', $data = '') {
    $data = str_replace("'", '`', $data);
    $data = str_replace("\n", '', $data);

    $result = '
            myMap.events.add(\'click\', function (e) {
                if (!myMap.balloon.isOpen()) {
                    var coords = e.get(\'coordPosition\');
                    myMap.balloon.open(coords, {
                        contentHeader: \'' . $title . '\',
                        contentBody: \' \' +
                            \'<p>\' + [
                            coords[0].toPrecision(6),
                            coords[1].toPrecision(6)
                            ].join(\', \') + \'</p> <form action="" method="POST"><input type="hidden" name="' . $name . '" value="\'+coords[0].toPrecision(6)+\', \'+coords[1].toPrecision(6)+\'">' . $data . '</form> \'
                 
                    });
                } else {
                    myMap.balloon.close();
                }
            });
            ';

    return ($result);
}

?>