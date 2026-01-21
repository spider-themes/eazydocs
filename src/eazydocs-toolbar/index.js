import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { BlockControls } from '@wordpress/block-editor';
import { Popover, ToolbarGroup, DropdownMenu } from '@wordpress/components';
import { insert, registerFormatType, removeFormat } from '@wordpress/rich-text';
import apiFetch from '@wordpress/api-fetch';
import './editor.scss';

const name = 'eazydocs/eazydocs-toolbar';

const EazyDocs_Toolbar = ({ isActive, value, onChange }) => {
    const [showPopover, setShowPopover] = useState(false);
    const [showEmbedPopup, setShowEmbedPopup] = useState(false);
    const [selectedValue, setSelectedValue] = useState('');
    const [selectedDoc, setSelectedDoc] = useState('');
    const [docsPosts, setDocsPosts] = useState([]);
    const conditionalItems = eazydocs_local_object?.ezd_get_conditional_items || [];
    const is_ezd_pro_block = eazydocs_local_object?.is_ezd_pro_block;

    useEffect(() => {
        apiFetch({ path: '/wp/v2/docs?per_page=100' }).then((posts) => {
            setDocsPosts(posts);
        });
    }, []);

    const isFootnotesUnlocked = eazydocs_local_object?.is_footnotes_unlocked === 'yes';

    // Footnotes Shortcode
    const reference = () => {
        if (!isFootnotesUnlocked) {
            Swal.fire({
                title: 'Opps...',
                html: 'This is a Promax feature. You need to <a href="admin.php?page=eazydocs-pricing"><strong class="upgrade-link">Upgrade&nbsp;&nbsp;➤</strong></a> to the Premium Version to use this feature',
                icon: "warning",
                buttons: [false, "Close"],
                dangerMode: true,
            });
            return;
        }

        if (isActive) {
            onChange(removeFormat(value, name));
            return;
        }
        const selectedText = value.text.slice(value.start, value.end);
        const shortcode = selectedText ? `[reference]${selectedText}[/reference]` : `[reference][/reference]`;
        onChange(insert(value, shortcode));
    };

    // Conditional Dropdown Shortcode
    const conditional_data = () => {
        setShowPopover(true);
    };

    const insertConditionalShortcode = () => {
        if (!selectedValue) return;
        const selectedText = value.text.slice(value.start, value.end);
        const shortcode = selectedText
            ? `[conditional_data dependency="${selectedValue}"]${selectedText}[/conditional_data]`
            : `[conditional_data dependency="${selectedValue}"][/conditional_data]`;
        onChange(insert(value, shortcode));
        setShowPopover(false);
    };

    // Embed Post Shortcode
    const embedPost = () => {
        setShowEmbedPopup(true);
    };

    const insertEmbedPostShortcode = () => {
        if (!selectedDoc) return;
        const shortcode = `[embed_post id="${selectedDoc}"]`;
        onChange(insert(value, shortcode));
        setShowEmbedPopup(false);
    };

    return (
        <>
            <BlockControls>
                <ToolbarGroup>
                    <DropdownMenu
                        className='eazydocs-toolbar__dropdown'
                        icon='ezd-icon'
                        label={__('Insert EazyDocs Shortcode', 'eazydocs')}
                        controls={[
                            {
                                title: (
                                    <span className={`ezd-menu-item-label ${!isFootnotesUnlocked ? 'ezd-item-locked' : ''}`}>
                                        {__('Footnotes', 'eazydocs')}
                                        {!isFootnotesUnlocked && <span className="ezd-badge-promax">Pro Max</span>}
                                    </span>
                                ),
                                onClick: reference
                            },
                            { title: __('Conditional Dropdown', 'eazydocs'), onClick: conditional_data },
                            ...(is_ezd_pro_block ? [{ title: __('Embed Post', 'eazydocs'), onClick: embedPost }] : [])
                        ]}
                    />
                </ToolbarGroup>
            </BlockControls>

            {/* Conditional Dropdown Popover */}
            {showPopover && (
                <Popover className='ezd-conditional-dropdown-tool' position="bottom center" onClose={() => setShowPopover(false)}>
                    <h4>{__('Select Condition', 'eazydocs')}</h4>
                    <select
                        value={selectedValue}
                        onChange={(e) => setSelectedValue(e.target.value)}
                    >
                        <option value="">{__('-- Select Option --', 'eazydocs')}</option>
                        {conditionalItems.map((item) => (
                            <option key={item.id} value={item.value}>{item.title}</option>
                        ))}
                    </select>
                    <button onClick={insertConditionalShortcode}>{__('Insert', 'eazydocs')}</button>
                </Popover>
            )}

            {/* Embed Post Popover */}
            {showEmbedPopup && (
                <Popover className='ezd-embed-post-tool' position="bottom center" onClose={() => setShowEmbedPopup(false)}>
                    <h4>{__('Select a Doc to Embed', 'eazydocs')}</h4>
                    <select value={selectedDoc} onChange={(e) => setSelectedDoc(e.target.value)}>
                        <option value="">{__('-- Select a Doc --', 'eazydocs')}</option>
                        {docsPosts.map((post) => (
                            <option key={post.id} value={post.id}>{post.title.rendered}</option>
                        ))}
                    </select>
                    <button onClick={insertEmbedPostShortcode}>{__('Insert', 'eazydocs')}</button>
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