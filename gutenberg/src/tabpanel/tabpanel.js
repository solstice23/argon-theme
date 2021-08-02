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
	Button,
	Panel, PanelBody, PanelRow
} from '@wordpress/components';

const { registerBlockType } = wp.blocks;

const getRandomString = (length) => {
	const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	let result = '';
	for (let i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
	return result;
}


registerBlockType('argon/tabpanel', {
	title: __('Tab 面板'),
	icon: 'table-row-after',
	category: 'argon',
	keywords: [
		'argon',
		__('Tab 面板'),
		'Tab'
	],
	attributes: {
		list: {
			type: 'array',
			default: [{
				title: '',
				content: '',
				id: 0,
				deleted: false
			}]
		},
		id: {
			type: 'string',
			default: ''
		},
		active: {
			type: 'number',
			default: 0
		},
		has_animation: {
			type: 'boolean',
			default: true
		},
	},
	edit: (props) => {
		if (props.attributes.id == '') {
			props.setAttributes({
				id: getRandomString(16)
			})
		}
		const addNode = () => {
			let newList = [
				...props.attributes.list,
				{
					time: '',
					title: '',
					content: '',
					id: props.attributes.list.length,
					deleted: false
				}
			]
			props.setAttributes({ list: newList });
		};
		const getClosestUndeleteItemId = (indexId) => {
			let id = 0;
			let minn = 2147483647;
			props.attributes.list.map((node) => {
				if (!node.deleted){
					let diff = Math.abs(node.id - indexId);
					if (diff < minn && diff){
						minn = diff;
						id = node.id;
					}
				}
			});
			return id;
		};
		const removeNode = (id) => {
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, deleted: true}
				}
				return node;
			});
			props.setAttributes({ list: newList });
			props.setAttributes({ active: getClosestUndeleteItemId(id) });
		}
		const onChangeTitle = (value, id) => {
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, title: value}
				}
				return node;
			});
			props.setAttributes({ list: newList });
		};
		const onChangeContent = (value, id) => {
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, content: value}
				}
				return node;
			});
			props.setAttributes({ list: newList });
		};
		const getUndeletedItemsCount = (list) => {
			let cnt = 0;
			list.map((node) => {
				if (!node.deleted){
					cnt++;
				}
			});
			return cnt;
		}
		const onHasAnimationChange = (value) => {
			props.setAttributes({ has_animation: value });
		}

		const tabTitleNodeList = props.attributes.list.map((node) => {
			if (!node.deleted){
				return (
					<li className="nav-item">
						<RichText
							tagName="a"
							className={"nav-link " + (props.attributes.active == node.id ? 'active' : '')} 
							placeholder={__("标题")}
							id={`#${props.attributes.id}-${node.id}-tab`}
							href={`#${props.attributes.id}-${node.id}`}
							value={node.title}
							onChange={(value) => onChangeTitle(value, node.id)}
							data-toggle="tab"
							role="tab"
							aria-controls={`#${props.attributes.id}-${node.id}`}
							aria-selected="false"
							onClick={() => {
								props.setAttributes({ active: node.id });
							}}
						/>
					</li>
				)
			}
		});

		const tabContentNodeList = props.attributes.list.map((node) => {
			if (!node.deleted && props.attributes.active == node.id){
				return (
					<div>
						<RichText
							tagName="div"
							placeholder={__("内容")}
							className={"tab-pane " + (props.attributes.active == node.id ? 'active show' : '')}
							id={`${props.attributes.id}-${node.id}`}
							role="tabpanel"
							aria-labelledby={`${props.attributes.id}-${node.id}-tab`}
							value={node.content}
							onChange={(value) => onChangeContent(value, node.id)}
						/>
						{getUndeletedItemsCount(props.attributes.list) > 1 &&
							<Button className="is-tertiary tab-remove-item-btn" onClick={() => removeNode(node.id)} style={{color: "#7889e8"}}><span className="dashicon dashicons dashicons-trash"></span>&nbsp;{__("移除此项")}</Button>
						}
					</div>
				)
			}
		});

		return (
			<div>
				<div className="argon-tabpanel">
					<div className="tabpanel-header nav-wrapper">
						<ul className="nav nav-pills nav-fill" role="tablist">
							{tabTitleNodeList}
							<li className="nav-item">
								<a className="nav-link" onClick={addNode} style={{color: "#fff", backgroundColor: "#7889e8", cursor: "pointer"}}>{__("+ 添加 Tab")}</a>
							</li>
						</ul>
					</div>
					<div className="tabpanel-body card-body shadow-sm">
						<div className="tab-content" id="myTabContent">
							{tabContentNodeList}
						</div>
					</div>
				</div>
				<InspectorControls key="setting">
					<PanelBody title={__("区块设置")} icon={"more"} initialOpen={true}>
						<PanelRow>
							<div id="gutenpride-controls">
								<fieldset>
									<PanelRow>{__('动画', 'argon')}</PanelRow>
									<ToggleControl
										label={__('切换时的淡入动画', 'argon')}
										checked={props.attributes.has_animation}
										onChange={onHasAnimationChange}
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
		const tabTitleNodeList = props.attributes.list.map((node) => {
			if (!node.deleted){
				return (
					<li className="nav-item">
						<a
							className={"nav-link " + (props.attributes.active == node.id ? 'active' : '')} 
							dangerouslySetInnerHTML={{__html: node.title}}
							id={`#${props.attributes.id}-${node.id}-tab`}
							href={`#${props.attributes.id}-${node.id}`}
							data-toggle="tab"
							role="tab"
							aria-controls={`#${props.attributes.id}-${node.id}`}
							aria-selected="false"
						/>
					</li>
				)
			}
		});

		const tabContentNodeList = props.attributes.list.map((node) => {
			if (!node.deleted){
				return (
					<div
						className={`tab-pane ${props.attributes.has_animation ? ' fade' : ''} ${props.attributes.active == node.id ? ' active show' : ''}`}
						dangerouslySetInnerHTML={{__html: node.content}}
						id={`${props.attributes.id}-${node.id}`}
						role="tabpanel"
						aria-labelledby={`${props.attributes.id}-${node.id}-tab`}>
					</div>
				)
			}
		});
		
		return (
			<div class="argon-tabpanel">
				<div class="tabpanel-header nav-wrapper">
					<ul class="nav nav-pills nav-fill" role="tablist">
						{tabTitleNodeList}
					</ul>
				</div>
				<div class="tabpanel-body card card-body shadow-sm">
					<div class="tab-content">
						{tabContentNodeList}
					</div>
				</div>
			</div>
		);
	},
});