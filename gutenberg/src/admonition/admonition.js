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
	ToggleControl,
	Panel, PanelBody, PanelRow
} from '@wordpress/components';
import { isWhitespaceCharacter } from 'is-whitespace-character'

const { registerBlockType } = wp.blocks;

registerBlockType('argon/admonition', {
	title: __('警告'),
	icon: 'text-page',
	category: 'argon',
	keywords: [
		'argon',
		__('警告')
	],
	attributes: {
		color: {
			type: 'string',
			default: '#7889e8'
		},
		title: {
			type: 'string',
			default: ''
		},
		content: {
			type: 'string',
			default: ''
		},
		fa_icon_name: {
			type: 'string',
			default: 'info-circle'
		},
		show_title: {
			type: 'bool',
			default: false
		},
		show_content: {
			type: 'bool',
			default: true
		},
	},
	edit: (props) => {
		const onChangeTitle = (value) => {
			props.setAttributes({ title: value });
		};
		const onChangeContent = (value) => {
			props.setAttributes({ content: value });
		};
		const onChangeColor = (value) => {
			props.setAttributes({ color: (value || "#7889e8") });
		}
		const onIconChange = (value) => {
			props.setAttributes({ fa_icon_name: value });
		}
		const onTitleStatusChange = (value) => {
			props.setAttributes({ show_title: value });
			if (!value) {
				props.setAttributes({ show_content: true });
			}
 		}
		const onContentStatusChange = (value) => {
			props.setAttributes({ show_content: value });
			if (!value) {
				props.setAttributes({ show_title: true });
			}
 		}

		return (
			<div>
				<div className="admonition shadow-sm" style={{ borderLeftColor: props.attributes.color }}>
					{props.attributes.show_title &&
						<div className="admonition-title" style={{ backgroundColor: props.attributes.color + "33" }}>
							{!(isWhitespaceCharacter(props.attributes.fa_icon_name) || props.attributes.fa_icon_name == "") &&
								<span>
									<i className={`fa fa-${props.attributes.fa_icon_name}`}></i>&nbsp;
								</span>
							}
							<RichText
								tagName="span"
								placeholder={__("标题")}
								value={props.attributes.title}
								onChange={onChangeTitle}
							/>
						</div>
					}
					{props.attributes.show_content &&
						<RichText
							tagName="div"
							className="admonition-body"
							placeholder={__("内容")}
							value={props.attributes.content}
							onChange={onChangeContent}
						/>
					}
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
											{ name: 'grey', color: '#888888' },
										]}
										value={props.attributes.color}
									/>
								</fieldset>
								<fieldset>
									<PanelRow>{__('部件', 'argon')}</PanelRow>
									<ToggleControl
										label={__('标题', 'argon')}
										checked={props.attributes.show_title}
										onChange={onTitleStatusChange}
									/>
									<ToggleControl
										label={__('内容', 'argon')}
										checked={props.attributes.show_content}
										onChange={onContentStatusChange}
									/>
								</fieldset>
								{props.attributes.show_title &&
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
								}
							</div>
						</PanelRow>
					</PanelBody>
				</InspectorControls>
			</div>
		);
	},
	save: (props) => {
		return (
			<div className="admonition shadow-sm" style={{ borderLeftColor: props.attributes.color }}>
				{props.attributes.show_title &&
					<div className="admonition-title" style={{ backgroundColor: props.attributes.color + "33" }}>
						{!(isWhitespaceCharacter(props.attributes.fa_icon_name) || props.attributes.fa_icon_name == "") &&
							<span>
								<i className={`fa fa-${props.attributes.fa_icon_name}`}></i>&nbsp;
							</span>
						}
						<span dangerouslySetInnerHTML={{ __html: props.attributes.title }}/>
					</div>
				}
				{props.attributes.show_content &&
					<div className="admonition-body" dangerouslySetInnerHTML={{ __html: props.attributes.content }}/>
				}
			</div>
		);
	},
});