<?php
include "header.php";
?>
<div class="content">
    <h3 class="map_header"></h3>
    <div class="map_content"></div>
    <div class="map_footer"></div>
</div>
<script>
    var svg = null;
    function loadRegion(id) {
        $.ajax({
            type: "POST",
            url: "http://api.turneon.ru/?method=region.get&id="+id,
            xhrFields: {withCredentials: true},
            success: function (data) {
                response = eval("(" + data + ")");
                if (response.result == "success") {
                    $(".map_header").text(response.name);
                    if(svg) {
                        $(".map_content").remove(svg);
                    }
                    bounds = {left: 0, top: 0, right: 0, bottom: 0};
                    svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                    $(svg).css({width: "1px", height: "1px"});
                    $(".map_content").append(svg);
                    for(elem in response.items) {
                        if(response.items[elem].path){
                            path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                            path.setAttribute('d', response.items[elem].path);
                            svg.appendChild(path);
                            rect = path.getBoundingClientRect();
                            if(elem == 0 || (bounds.left > rect.left)) bounds.left = rect.left;
                            if(elem == 0 || (bounds.top > rect.top)) bounds.top = rect.top;
                            if(elem == 0 || (bounds.right < rect.right)) bounds.right = rect.right;
                            if(elem == 0 || (bounds.bottom < rect.bottom)) bounds.bottom = rect.bottom;
                        }
                    }
                }
            }
        });
    }

    $(document).ready(function(){
        current_region = $.cookie('region');
        if(current_region)
            loadRegion(current_region);
        else
            loadRegion('auto');
    });
</script>
<?php
include "footer.php";