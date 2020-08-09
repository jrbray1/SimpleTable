// Idea from https://www.mediawiki.org/wiki/Extension_talk:SimpleTable/Archive#Adding_a_SimpleTable_button_to_the_editor
// Icon from https://en.wikipedia.org/wiki/User:MarkS/Extra_edit_buttons
// https://www.mediawiki.org/wiki/Manual_talk:Custom_edit_buttons#Getting_a_bit_complicated,_doesn't_it?

if ( ['edit', 'submit'].indexOf( mw.config.get( 'wgAction' ) ) !== -1 ) {
    mw.loader.using( 'mediawiki.action.edit', function () {
        $( function ( $ ) {
            if ( mw.toolbar ) {
                mw.toolbar.addButton( {
        imageFile: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABcAAAAWCAIAAACkFJBSAAAAB3RJTUUH1gMHEyYhQxOZqAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAARnQU1BAACxjwv8YQUAAAE3SURBVHjaY/z//z8DZSCnZiILkLrxgiKD7j59DzLl91+KT
        speedTip: 'SimpleTable',
        tagOpen: "<tab class='wikitable sortable' sep=tab head=top width=600>\n",
        tagClose: "\n</tab>",
        sampleText: 'style="text-align:left"|Header1	style="text-align:left"|Header2\nFieldA	FieldB',
        imageId: 'button-simpletable'
            });
            }
        } );
    } );
}
