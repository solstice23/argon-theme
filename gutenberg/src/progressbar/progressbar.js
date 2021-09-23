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

const { registerBlockType } = wp.blocks;

const clamp = (num, min, max) => Math.min(Math.max(num, min), max);

registerBlockType('argon/progressbar', {
	title: __('进度条'),
	icon: 'chart-bar',
	category: 'argon',
	keywords: [
		'argon',
		__('进度条')
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
		progress: {
			type: 'number',
			default: 50
		},
		show_title: {
			type: 'bool',
			default: true
		}
	},
	edit: (props) => {
		const onChangeTitle = (value) => {
			props.setAttributes({ title: value });
		};
		const onChangeColor = (value) => {
			props.setAttributes({ color: (value || "#5e72e4") });
		}
		const onChangeProgress = (value) => {
			props.setAttributes({ progress: clamp(Number(value), 0, 100) });
		}
		const onTitleStatusChange = (value) => {
			props.setAttributes({ show_title: value });
		}
		return (
			<div>
				<div class="progress-wrapper">
					<div class="progress-info">
						{props.attributes.show_title &&
							<div class="progress-label">
								<RichText
									tagName="span"
									placeholder={__("标题")}
									value={props.attributes.title}
									onChange={onChangeTitle}
								/>
							</div>
						}
						<div class="progress-percentage">
							<span>{props.attributes.progress}%</span>
						</div>
					</div>
					<div class="progress">
						<div class="progress-bar" style={{ width: props.attributes.progress.toString() + "%", backgroundColor: props.attributes.color }} />
					</div>
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
											{ name: 'argon', color: '#5e72e4' },
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
									<PanelRow>{__('部件', 'argon')}</PanelRow>
									<ToggleControl
										label={__('标题', 'argon')}
										checked={props.attributes.show_title}
										onChange={onTitleStatusChange}
									/>
								</fieldset>
								<fieldset>
									<PanelRow>{__('进度 (%)', 'argon')}</PanelRow>
									<input type="number" min="0" max="100" step="1" value={props.attributes.progress} onChange={e => onChangeProgress(e.currentTarget.value)}/>
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
			<div class="progress-wrapper">
				<div class="progress-info">
					{props.attributes.show_title &&
						<div class="progress-label">
							<span dangerouslySetInnerHTML={{ __html: props.attributes.title }}/>
						</div>
					}
					<div class="progress-percentage">
						<span>{props.attributes.progress}%</span>
					</div>
				</div>
				<div class="progress">
					<div class="progress-bar" style={{ width: props.attributes.progress.toString() + "%", backgroundColor: props.attributes.color }} />
				</div>
			</div>
		);
	},
});
