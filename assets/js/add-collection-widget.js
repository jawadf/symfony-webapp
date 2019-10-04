var $collectionHolder;

// setup an "add an image" button/link
var $addImageButton = $('<button type="button" class="btn btn-info add_image_link">Add more images</button>');
var $newLinkDiv = $('<div></div>').append($addImageButton);


jQuery(document).ready(function() {
    // Get the ul that holds the collection of images
    $collectionHolder = $('ul.images');

    // add the "Add an image" anchor and li to the images ul
    $collectionHolder.append($newLinkDiv);

    // add a delete link to all of the existing tag form li elements
    $collectionHolder.find('li').each(function() {
        addTagFormDeleteLink($(this));
    });

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addImageButton.on('click', function(e) {
        // add a new Image form (see next code block)
        addImageForm($collectionHolder, $newLinkDiv);
    });
});






function addImageForm($collectionHolder, $newLinkDiv) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);


    // Display the form in the page in an li, before the "Add an image" link li
    var $newFormLi = $('<li></li>').append(newForm);
    $newLinkDiv.before($newFormLi);

    // add a delete link to the new form
    addTagFormDeleteLink($newFormLi);
}


function addTagFormDeleteLink($formLi) {
    var $removeFormButton = $('<button class="btn btn-danger" type="button">X</button>');
    $formLi.append($removeFormButton);

    $removeFormButton.on('click', function(e) {
        // remove the li for the tag form
        $formLi.remove();
    });
}






/***************************************/
        
/***************************************/

/*

jQuery(document).ready(function () {
    jQuery('.add-another-collection-widget').click(function (e) {
        var list = jQuery(jQuery(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter);
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = jQuery(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);
    });
});

*/
