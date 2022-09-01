var skeletonId = "skeleton";
var contentId = "content";
var skipCounter = 0;
var takeAmount = 10;

var page_no = 1;
var has_more = true;
function loadMore(type, user_id) {
    if (has_more) {
        $("#skeleton_" + type).removeClass("d-none");
        $.ajax({
            url: "/requests",
            type: "get",
            data: {
                page: page_no + 1,
                type: type,
            },
            success: function (res) {
                $("#skeleton_" + type).addClass("d-none");

                var e = "";
                if (res.data.length) {
                    $.each(res.data, function (key, val) {
                        if (type == "sent") {
                            e =
                                e +
                                '<div id="' +
                                type +
                                "_" +
                                val.id +
                                '" class="d-flex justify-content-between"><table class="ms-1"><td class="align-middle">' +
                                val.receiver[0].name +
                                '</td><td class="align-middle"> - </td><td class="align-middle">' +
                                val.receiver[0].email +
                                '</td><td class="align-middle"></div> </table><div><button id="cancel_request_btn_" class="btn btn-danger me-1" onclick=withdrawRequest(' +
                                val.id +
                                ")>Withdraw Request</button></div></div>";
                        }
                        if (type == "received") {
                            e =
                                e +
                                '<div class="d-flex justify-content-between"><table class="ms-1"><td class="align-middle">' +
                                val.name +
                                '</td><td class="align-middle"> - </td><td class="align-middle">' +
                                val.email +
                                '</td><td class="align-middle"></div> </table><div><button id="accept_request_btn_" class="btn btn-primary me-1" onclick="">Accept</button></div></div>';
                        }
                        if (type == "suggestions") {
                            e =
                                e +
                                '<div class="d-flex justify-content-between"><table class="ms-1"><td class="align-middle">' +
                                val.name +
                                '</td><td class="align-middle"> - </td><td class="align-middle">' +
                                val.email +
                                '</td><td class="align-middle"></div> </table><div><button onclick=handleAction(' +
                                val.id +
                                ') id="create_request_btn_" class="btn btn-primary me-1">Connect</button></div></div>';
                        }
                        if (type == "connections") {
                            // var commonConnections = Object.keys(val.commonConnections).map(function (key) { return val.commonConnections[key]; });
                            if (user_id != val.sender[0].id) {
                                e =
                                    e +
                                    '<div class="d-flex justify-content-between"><table class="ms-1"><td class="align-middle">' +
                                    val.sender[0].name +
                                    '</td><td class="align-middle"> - </td><td class="align-middle">' +
                                    val.sender[0].email +
                                    '</td><td class="align-middle"></div> </table><div><div><button style="width: 220px" id="get_connections_in_common_" class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_" aria-expanded="false" aria-controls="collapseExample">Connections in common (' +
                                    val.commonConnections.data.length +
                                    ')</button> <button id="create_request_btn_" onclick=withdrawRequest(' +
                                    val.id +
                                    ',"connection") class="btn btn-danger me-1">Remove Connection</button></div></div></div>';
                            } else {
                                e =
                                    e +
                                    '<div class="d-flex justify-content-between"><table class="ms-1"><td class="align-middle">' +
                                    val.receiver[0].name +
                                    '</td><td class="align-middle"> - </td><td class="align-middle">' +
                                    val.receiver[0].email +
                                    '</td><td class="align-middle"></div> </table><div><div><button style="width: 220px" id="get_connections_in_common_" class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_" aria-expanded="false" aria-controls="collapseExample">Connections in common (' +
                                    val.commonConnections.data.length +
                                    ')</button> <button id="create_request_btn_" onclick=withdrawRequest(' +
                                    val.id +
                                    ',"connection") class="btn btn-danger me-1">Remove Connection</button></div></div></div>';
                            }
                        }
                    });
                }
                $("#" + type).append(e);
                page_no = page_no + 1;
                if (res.last_page == page_no) {
                    has_more = false;
                    $("#load_more_btn_parent_" + type).addClass("d-none");
                }
            },
            error: function (textStatus, errorThrown) {
                $("#skeleton_" + type).addClass("d-none");
            },
        });
    }
}

function handleAction(id) {
    var csrf_js_var = $('meta[name="csrf-token"]').attr("content");
    $("<form>", {
        id: "add-connection",
        html:
            '<input type="text" id="id" name="id" value="' +
            id +
            '" /><input name="_token" value="' +
            csrf_js_var +
            '" type="hidden">',
        action: "/requests",
        method: "post",
    })
        .appendTo(document.body)
        .submit();
}

function acceptConnection(id) {
    $.ajax({
        url: "/requests/" + id,
        type: "patch",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            window.location.replace("/home?type=received");
        },
        error: function (textStatus, errorThrown) {},
    });
}

function withdrawRequest(id, type = null) {
    $.ajax({
        url: "/requests/" + id,
        type: "delete",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (res) {
            if (type) {
                window.location.replace("/home?type=connections");
            } else {
                window.location.replace("/home?type=received");
            }
        },
        error: function (textStatus, errorThrown) {},
    });
}
