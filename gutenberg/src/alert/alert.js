import './editor.scss';
import { __ } from './../i18n/i18n.js';
import {
	RichText,
	MediaUpload,
	BlockControls,
	AlignmentToolbar,
	RichTextToolbarButton,
	InspectorControls,
	PanelColorSettings
} from "@wordpress/block-editor";
import {
	ColorPalette,
	TextControl,
	Panel, PanelBody, PanelRow
} from '@wordpress/components';
import { isWhitespaceCharacter } from 'is-whitespace-character'

const { registerBlockType } = wp.blocks;

registerBlockType('argon/alert', {
	title: __('提示'),
	icon: 'warning',
	category: 'argon',
	keywords: [
		'argon',
		__('提示')
	],
	attributes: {
		color: {
			type: 'string',
			default: '#7889e8'
		},
		content: {
			type: 'string',
			default: ''
		},
		fa_icon_name: {
			type: 'string',
			default: 'info-circle'
		},
	},
	edit: (props) => {
		const onChangeContent = (value) => {
			props.setAttributes({ content: value });
		};
		const onChangeColor = (value) => {
			props.setAttributes({ color: (value || "#7889e8") });
		}
		const onIconChange = (value) => {
			props.setAttributes({ fa_icon_name: value });
		}

		return (
			<div>
				<div className="alert" style={{ backgroundColor: props.attributes.color }}>
					{!(isWhitespaceCharacter(props.attributes.fa_icon_name) || props.attributes.fa_icon_name == "") &&
						<span className="alert-inner--icon">
							<i className={`fa fa-${props.attributes.fa_icon_name}`}></i>
						</span>
					}
					<RichText
						tagName="span"
						className="alert-inner--text"
						placeholder={__("内容")}
						value={props.attributes.content}
						onChange={onChangeContent}
					/>
				</div>
				<InspectorControls key="setting">
					<PanelBody title={__("区块设置")} icon={"more"} initialOpen={true}>
						<PanelRow>
							<div id="gutenpride-controls">
								<fieldset>
									<PanelRow>{__('颜色', 'argon')}</PanelRow>
									<ColorPalette
										onChange={onChangeColor}
										colors={[
											{ name: 'argon', color: '#7889e8' },
											{ name: 'green', color: '#4fd69c' },
											{ name: 'red', color: '#f75676' },
											{ name: 'blue', color: '#37d5f2' },
											{ name: 'orange', color: '#fc7c5f' },
											{ name: 'pink', color: '#fa7298' },
											{ name: 'black', color: '#3c4d69' },
										]}
										value={props.attributes.color}
									/>
								</fieldset>
								<fieldset>
									<PanelRow>{__('图标', 'argon')}</PanelRow>
									<TextControl
										value={props.attributes.fa_icon_name}
										onChange={onIconChange}
									/>
									<p className="help-text">
										{__('Font Awesome 中的图标名，留空则不显示', 'argon')}&nbsp;
										<a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">{__('浏览图标', 'argon')}</a>
									</p>
								</fieldset>
							</div>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},
	save: (props) => {
		return (
			<div className="alert" style={{ backgroundColor: props.attributes.color }}>
				{!(isWhitespaceCharacter(props.attributes.fa_icon_name) || props.attributes.fa_icon_name == "") &&
					<span className="alert-inner--icon">
						<i className={`fa fa-${props.attributes.fa_icon_name}`}></i>
					</span>
				}
				<span className="alert-inner--text" dangerouslySetInnerHTML={{ __html: props.attributes.content }}></span>
			</div>
		);
	},
});
