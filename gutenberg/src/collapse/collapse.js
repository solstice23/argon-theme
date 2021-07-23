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

const calcTranslucentColor = (color) => {
	if (color.length == 7){
		return color + "33";
	}
	return color;
}

registerBlockType('argon/collapse', {
	title: __('折叠区块'),
	icon: 'align-wide',
	category: 'argon',
	keywords: [
		'argon',
		__('折叠区块')
	],
	attributes: {
		color: {
			type: 'string',
			default: '#ffffff00'
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
			default: ''
		},
		collapsed: {
			type: 'bool',
			default: true
		},
		show_left_border: {
			type: 'bool',
			default: false
		}
	},
	edit: (props) => {
		const onChangeTitle = (value) => {
			props.setAttributes({ title: value });
		};
		const onChangeContent = (value) => {
			props.setAttributes({ content: value });
		};
		const onChangeColor = (value) => {
			props.setAttributes({ color: (value || "#ffffff00") });
		}
		const onIconChange = (value) => {
			props.setAttributes({ fa_icon_name: value });
		}
		const onCollapsedStatusChange = (value) => {
			props.setAttributes({ collapsed: value });
 		}
		const onLeftBorderStatusChange = (value) => {
			props.setAttributes({ show_left_border: value });
 		}

		return (
			<div>
				<div className={`collapse-block shadow-sm collapsed ${props.attributes.show_left_border ? "" : "hide-border-left"}`} style={{ borderLeftColor: props.attributes.color }}>
					<div className="collapse-block-title" style={{ backgroundColor: calcTranslucentColor(props.attributes.color) }}>
						{!(isWhitespaceCharacter(props.attributes.fa_icon_name) || props.attributes.fa_icon_name == "") &&
							<span>
								<i className={`fa fa-${props.attributes.fa_icon_name}`}></i>&nbsp;
							</span>
						}
						<RichText
							tagName="span"
							className="collapse-block-title-inner"
							placeholder={__("标题")}
							value={props.attributes.title}
							onChange={onChangeTitle}
						/>
						<i className="collapse-icon fa fa-angle-up"></i>
					</div>
					<RichText
						tagName="div"
						className="collapse-block-body"
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
											{ name: 'transparent', color: '#ffffff00' },
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
									<ToggleControl
										label={__('左边框', 'argon')}
										checked={props.attributes.show_left_border}
										onChange={onLeftBorderStatusChange}
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
								<fieldset>
									<PanelRow>{__('状态', 'argon')}</PanelRow>
									<ToggleControl
										label={__('默认折叠', 'argon')}
										checked={props.attributes.collapsed}
										onChange={onCollapsedStatusChange}
									/>
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
			<div className={`collapse-block shadow-sm ${props.attributes.collapsed ? "collapsed" : ""} ${props.attributes.show_left_border ? "" : "hide-border-left"}`}  style={{ borderLeftColor: props.attributes.color }}>
				<div className="collapse-block-title" style={{ backgroundColor: calcTranslucentColor(props.attributes.color) }}>
					{!(isWhitespaceCharacter(props.attributes.fa_icon_name) || props.attributes.fa_icon_name == "") &&
						<span>
							<i className={`fa fa-${props.attributes.fa_icon_name}`}></i>&nbsp;
						</span>
					}
					<span className="collapse-block-title-inner" dangerouslySetInnerHTML={{ __html: props.attributes.title }}/>
					<i className="collapse-icon fa fa-angle-down"></i>
				</div>
				<div className="collapse-block-body" style={props.attributes.collapsed ? "display: none" : ""} dangerouslySetInnerHTML={{ __html: props.attributes.content }}/>
			</div>
		);
	},
});