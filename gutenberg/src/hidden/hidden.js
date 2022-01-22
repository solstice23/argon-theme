import './editor.scss';
import { __ } from './../i18n/i18n.js';
import {
	RichText,
	MediaUpload,
	BlockControls,
	AlignmentToolbar,
	RichTextToolbarButton,
	InspectorControls,
	PanelColorSettings,
} from '@wordpress/block-editor';
import {
	ColorPalette,
	TextControl,
	ToggleControl,
	Panel, PanelBody, PanelRow,
} from '@wordpress/components';

const { registerBlockType } = wp.blocks;

registerBlockType( 'argon/hidden', {
	title: __( '隐藏内容' ),
	icon: 'hidden fa fa-eye-slash',
	category: 'argon',
	keywords: [
		'argon',
		__( '隐藏内容' ),
	],
	attributes: {
		content: {
			type: 'string',
			default: '',
		},
	},
	edit: ( props ) => {
		const onChangeContent = ( value ) => {
			props.setAttributes( { content: value } );
		};

		return (
			<div>
				<span style={ { color: '#673bb7' } }>隐藏内容</span>
				<div style={ { padding: '10px', border: '2px dashed #673bb7' } } >
					<RichText
						tagName="span"
						className="alert-inner--text"
						placeholder={ __( '内容' ) }
						value={ props.attributes.content }
						onChange={ onChangeContent }
					/>
				</div>

				<InspectorControls key="setting">
					<PanelBody title={ __( '区块设置' ) } icon={ 'more' } initialOpen={ true }>
						<PanelRow>
							<div id="gutenpride-controls">

							</div>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},
	save: ( props ) => {
		return (
			<div dangerouslySetInnerHTML={ { __html: '[hide]' + props.attributes.content + '[/hide]' } }></div>
		);
	},
} );
