/*
 * @author John Snook
 * @date Jul 2, 2018
 * @license https://snooky.biz/site/license
 * @copyright 2018 John Snook Consulting
 * The problem with tabbed interfaces is you can't see what you're going to get.
 * With stax, your "tab panes" are displayed in a stack, giving the user a peek.
 *
 * This needs to be made into a plugin, I just can't be bothered right now
 */
var stack = 'div.stack';
var stackItems = stack + ' > div.stackItem';

$(stack).css('position', 'relative');
$(stackItems).css('position', 'absolute');

function stackEm() {
    var i = 1;
    var count = ($(stackItems)).length;
    var containerWidth = $("#StackAttack").innerWidth();
    var gutterSize = 50;
    var itemWidth = containerWidth - (count * gutterSize) - gutterSize;

    $(stackItems).each(function () {
        $(this)
                .off('click')
                .width(itemWidth)
                .css('z-index', i + 1)
                .css('left', gutterSize * i)
                .css('top', gutterSize * i)
                .fadeIn(200);

        $(this).on('click', function (event) {
            let topIndex = count - 1;
            if ($(this).index() < topIndex) {
                event.stopPropagation();
                $(this).fadeOut(200, function () {
                    $(this).insertAfter($(stackItems).eq(topIndex));
                    stackEm();
                });
            }
        });
        i++;
    });
}

stackEm();

$(window).on('resize', function () {
    stackEm();
});