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

registerBlockType('argon/timeline', {
	title: __('时间线'),
	icon: 'clock',
	category: 'argon',
	keywords: [
		'argon',
		__('时间线')
	],
	attributes: {
		list: {
			type: 'array',
			default: [{
				time: '',
				title: '',
				content: '',
				id: 0,
				deleted: false
			}]
		}
	},
	edit: (props) => {
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
		const removeNode = (id) => {
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, deleted: true}
				}
				return node;
			});
			props.setAttributes({ list: newList });
		}
		const onChangeTime = (value, id) => {
			let newList = props.attributes.list.map((node) => {
				if (node.id == id){
					return {...node, time: value}
				}
				return node;
			});
			props.setAttributes({ list: newList });
		};
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

		const nodelist = props.attributes.list.map((node) => {
			if (node.deleted == true){
				return;
			}
			return (
				<div class="argon-timeline-node" key={node.id.toString()}>
					<RichText
						tagName="div"
						className="argon-timeline-time"
						placeholder={__("时间")}
						value={node.time}
						onChange={value => onChangeTime(value, node.id)}
					/>
					<div class="argon-timeline-card bg-gradient-secondary shadow-sm">
						<RichText
							tagName="div"
							className="argon-timeline-title"
							placeholder={__("标题")}
							value={node.title}
							onChange={value => onChangeTitle(value, node.id)}
						/>
						<RichText
							tagName="div"
							className="argon-timeline-content"
							placeholder={__("内容")}
							value={node.content}
							onChange={value => onChangeContent(value, node.id)}
						/>
						{getUndeletedItemsCount(props.attributes.list) > 1 &&
							<Button className="is-tertiary timeline-remove-item-btn" onClick={() => removeNode(node.id)} style={{color: "#7889e8"}}><span class="dashicon dashicons dashicons-trash"></span></Button>
						}
					</div>
				</div>
			);
		});

		return (
			<div>
				<div class="argon-timeline">
					{nodelist}
					<Button className="is-primary" onClick={addNode}  style={{marginTop: 8, backgroundColor: "#7889e8"}}>{__("+ 添加节点")}</Button>
				</div>
			</div>
		);
	},
	save: (props) => {
		const nodelist = props.attributes.list.map((node) => {
			if (node.deleted == true){
				return;
			}
			return (
				<div class="argon-timeline-node">
					{(node.time != "") &&
						<div className="argon-timeline-time" dangerouslySetInnerHTML={{ __html: node.time }}></div>
					}
					<div class="argon-timeline-card card bg-gradient-secondary shadow-sm">
						{(node.title != "") &&
							<div className="argon-timeline-title" dangerouslySetInnerHTML={{ __html: node.title }}></div>
						}
						{(node.content != "") &&
							<div className="argon-timeline-content" dangerouslySetInnerHTML={{ __html: node.content }}></div>
						}
					</div>
				</div>
			);
		});
		return (
			<div>
				<div class="argon-timeline">
					{nodelist}
				</div>
			</div>
		);
	},
});