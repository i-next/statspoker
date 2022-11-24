$(document).ready(function () {
    $(".filter_cards").on('change', function () {
        $.ajax({
            type: "GET",
            url: $("#sortcards").data('url'),
            data: {'sort': $("#sortcards option:selected").val(), 'size': $("#sizecards option:selected").val()},
            success: function (response) {
                $("#cards_stats").html(response);
            }
        });

    });

    $(".filter_hands").on('change', function () {
        $.ajax({
            type: "GET",
            url: $("#sorthands").data('url'),
            data: {'sort': $("#sorthands option:selected").val(), 'size': $("#sizehands option:selected").val()},
            success: function (response) {
                $("#hands_stats").html(response);
            }
        });

    });
});