import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { BlockControls } from '@wordpress/block-editor';
import { Popover, ToolbarGroup, ToolbarButton, DropdownMenu } from '@wordpress/components';
import { insert, registerFormatType, removeFormat, toggleFormat } from '@wordpress/rich-text';
import './editor.scss';

const name = 'eazydocs/eazydocs-toolbar';

const EazyDocs_Toolbar = ({ isActive, value, onChange }) => {
    const [showPopover, setShowPopover] = useState(false);
    const [numberValue, setNumberValue] = useState('');
    const conditionalItems = eazydocs_local_object.ezd_get_conditional_items;

    const dataItems = conditionalItems.map((item) => (
        <option key={item.id} value={item.value}>{item.title}</option>
    ));
    
    // Footnotes
    const reference = () => {
        if (isActive) {
            onChange(removeFormat(value, name));
            return;
        }

        const selectedText  = value.text.slice(value.start, value.end);
        let shortcode       = '';

        // Get the number of footnotes in the editor
        let shortcodeNumber = jQuery('.is-root-container p').text().match(/\[reference number="(\d+)"\]/g);
        if (shortcodeNumber !== null) {
            shortcodeNumber = shortcodeNumber.length + 1;
        } else {
            shortcodeNumber = 1;
        }

        // Wrap selected text with shortcode if text is selected
        if (selectedText) {
            shortcode = `[reference number="${shortcodeNumber}"]${selectedText}[/reference]`;
        } else {
            // Insert shortcode at cursor position if no text is selected
            shortcode = `[reference number="${shortcodeNumber}"][/reference]`;
        }
        
        onChange(insert(value, shortcode));
    };
  
    // Conditional Dropdown
    const conditional_data = () => {
        if (isActive) {
            onChange(removeFormat(value, name));
            return;
        }
        setShowPopover(true);
    };
    
    // Insert shortcode with the selected value into the rich text
    const ezdToolbarDropDown = (selectedValue) => {
        // Insert shortcode with the selected value into the rich text
        const shortcodeNumber = shortcodeCounter;
        setShortcodeCounter(shortcodeCounter + 1);

        const selectedText  = value.text.slice(value.start, value.end);
        let shortcode       = '';
        
        // Wrap selected text with shortcode if text is selected
        if (selectedText) {
            shortcode = `[conditional_data dependency="${selectedValue}"]${selectedText}[/conditional_data]`;
        } else {
            // Insert shortcode at cursor position if no text is selected
            shortcode = `[conditional_data dependency="${selectedValue}"][/conditional_data]`;
        }
        
        onChange(insert(value, shortcode));

        // Hide the popover after insertion
        setShowPopover(false);
    };

    return (
        <>
            <BlockControls>
                <ToolbarGroup>
                    <DropdownMenu
                    className='eazydocs-toolbar__dropdown'                     
                    icon= 'ezd-icon'
                    label={__('Insert EazyDocs Shortcode', 'eazydocs')}
                    controls={[                    
                        {
                            title: __('Footnotes', 'eazydocs'),
                            onClick: reference
                        },
                        {
                            title: __('Conditional Dropdown', 'eazydocs'),
                            onClick: conditional_data,
                        },
                    ]}
                    />
                </ToolbarGroup>
            </BlockControls>
            
            {showPopover && (
                <Popover className='ezd-conditional-dropdown-tool' position="bottom center" onClose={() => setShowPopover(false)}>
                <select 
                    value={numberValue}
                    onChange={(e) => setNumberValue(e.target.value)}
                >
                    <option value="">-- Select Option --</option>
                    {dataItems}
                    </select>
                <button onClick={() => ezdToolbarDropDown(numberValue)}>Insert</button>
            </Popover>
            )}
            
        </>
    );
};

registerFormatType(name, {
    title: __('EazyDocs Toolbar', 'eazydocs'),
    tagName: 'span',
    className: 'eazydocs-toolbar',
    edit: EazyDocs_Toolbar
});