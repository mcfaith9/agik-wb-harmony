<script setup>
	import { ref, computed } from "vue"
	import { useRoute, useRouter } from "vue-router"
	import { 
		Calendar1,
		Settings,
		LibraryBig,
		Fingerprint,
		UsersRound,
		UserRoundCog,
		LayoutDashboard,
		ScrollText,
		ChartGantt,
		Scroll,
		ChevronDown,
		Ellipsis,
		Trophy,
		Handshake,
		SlidersVertical,
		Wallet, 
		Heart
	} from "lucide-vue-next"
	import { useSidebar } from "@/composables/useSidebar"

	const route = useRoute()
	const router = useRouter()

	const { isExpanded, isMobileOpen, isHovered, openSubmenu } = useSidebar()

	const menuGroups = [
		{
			title: "Menu",
			items: [
				{
					icon: LibraryBig,
					name: "Work",
					subItems: [
						// { icon: LayoutDashboard, name: "Dashboard", path: "/", pro: false },
						{ icon: ScrollText, name: "Tasks", path: "/tasks", pro: false },
						{ icon: ChartGantt, name: "Gantt Chart", path: "/tasks-gantt-chart", pro: false },
						{ icon: Scroll, name: "Projects", path: "/projects", pro: false },
					],
				},
				{
					icon: Calendar1,
					name: "Calendar",
					path: "/calendar",
				},
				{
					icon: Trophy,
					name: "Gamify",
					path: "/gamify",
				},
			],
		},
		{
			title: "Others",
			items: [
				{
					icon: Fingerprint,
					name: "Admin",
					subItems: [
						{ icon: UsersRound, name: "Users", path: "/admin/users", pro: false },
						{ icon: UserRoundCog, name: "Roles", path: "/admin/roles", pro: false },
						{ icon: Handshake, name: "Teams", path: "/admin/teams", pro: false },		
						{ icon: Heart, name: "Health", path: "/admin/health", pro: false },				
						{ icon: Wallet, name: "Accounting", path: "/admin/accounting", pro: false },
						{ icon: SlidersVertical, name: "Settings", path: "/admin/settings", pro: false },
					],
				},
				{
					icon: Settings,
					name: "Settings",
					path: "/settings",
				},
				// ... Add other menu items here
			],
		},
	]

	const isActive = (path) => route.path === path

	const toggleSubmenu = (groupIndex, itemIndex) => {
		const item = menuGroups[groupIndex].items[itemIndex]
		const key = `${groupIndex}-${itemIndex}`

		if (openSubmenu.value === key) return

		if (item.subItems?.length) {
			const firstSubItem = item.subItems[0]
			// Navigate to the first subItem if not already active
			if (route.path !== firstSubItem.path) {
				router.push(firstSubItem.path)
			}
		}

		openSubmenu.value = openSubmenu.value === key ? null : key
	}

	const isAnySubmenuRouteActive = computed(() => {
		return menuGroups.some((group) =>
			group.items.some(
				(item) =>
					item.subItems && item.subItems.some((subItem) => isActive(subItem.path))
			)
		)
	})

	const isSubmenuOpen = (groupIndex, itemIndex) => {
		const key = `${groupIndex}-${itemIndex}`
		return (
			openSubmenu.value === key ||
			(isAnySubmenuRouteActive.value &&
				menuGroups[groupIndex].items[itemIndex].subItems?.some((subItem) =>
					isActive(subItem.path)
				))
		)
	}

	const startTransition = (el) => {
		el.style.height = "auto"
		const height = el.scrollHeight
		el.style.height = "0px"
		el.offsetHeight // force reflow
		el.style.height = height + "px"
	}

	const endTransition = (el) => {
		el.style.height = ""
	}
</script>

<template>
	<aside
		:class="[
			'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-99999 border-r border-gray-200',
			{
				'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
				'lg:w-[90px]': !isExpanded && !isHovered,
				'translate-x-0 w-[290px]': isMobileOpen,
				'-translate-x-full': !isMobileOpen,
				'lg:translate-x-0': true,
			},
		]">
		<div
			:class="[
				'py-8 flex',
				!isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
			]">
			<router-link to="/">
				<img
					v-if="isExpanded || isHovered || isMobileOpen"
					class="dark:hidden"
					src="@/images/logo/logo.svg"
					alt="Logo"
					width="150"
					height="40"
				/>
				<img
					v-if="isExpanded || isHovered || isMobileOpen"
					class="hidden dark:block"
					src="@/images/logo/logo-dark.svg"
					alt="Logo"
					width="150"
					height="40"
				/>
				<img
					v-else
					src="@/images/logo/logo-icon.svg"
					class="rounded-full"
					alt="Logo"
					width="32"
					height="32"
				/>
			</router-link>
		</div>
		<div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
			<nav class="mb-6">
				<div class="flex flex-col gap-4">
					<div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex">
						<h2
							:class="[
								'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
								!isExpanded && !isHovered
									? 'lg:justify-center'
									: 'justify-start',
							]">
							<template v-if="isExpanded || isHovered || isMobileOpen">
								{{ menuGroup.title }}
							</template>
							<Ellipsis v-else />
						</h2>
						<ul class="flex flex-col gap-4">
							<li v-for="(item, index) in menuGroup.items" :key="item.name">
								<button
									v-if="item.subItems"
									@click="toggleSubmenu(groupIndex, index)"
									:class="[
										'menu-item group w-full',
										{
											'menu-item-active': isSubmenuOpen(groupIndex, index),
											'menu-item-inactive': !isSubmenuOpen(groupIndex, index),
										},
										!isExpanded && !isHovered
											? 'lg:justify-center'
											: 'lg:justify-start',
									]">
									<span
										:class="[
											isSubmenuOpen(groupIndex, index)
												? 'menu-item-icon-active'
												: 'menu-item-icon-inactive',
										]">
										<component :is="item.icon" class="w-5 h-5" />
									</span>
									<span
										v-if="isExpanded || isHovered || isMobileOpen"
										class="menu-item-text"
										>{{ item.name }}</span
									>
									<ChevronDown
										v-if="isExpanded || isHovered || isMobileOpen"
										:class="[
											'ml-auto w-5 h-5 transition-transform duration-200',
											{
												'rotate-180 text-brand-500': isSubmenuOpen(
													groupIndex,
													index
												),
											},
										]"
									/>
								</button>
								<router-link
									v-else-if="item.path"
									:to="item.path"
									:class="[
										'menu-item group items-center',
										{
											'menu-item-active': isActive(item.path),
											'menu-item-inactive': !isActive(item.path),
										},
									]">
									<span
										:class="[
											isActive(item.path)
												? 'menu-item-icon-active'
												: 'menu-item-icon-inactive',
										]">
										<component :is="item.icon" class="w-5 h-5" />
									</span>
									<span
										v-if="isExpanded || isHovered || isMobileOpen"
										class="menu-item-text">
										{{ item.name }}
									</span>
								</router-link>
								<transition
									@enter="startTransition"
									@after-enter="endTransition"
									@before-leave="startTransition"
									@after-leave="endTransition">
									<div
										v-show="
											isSubmenuOpen(groupIndex, index) &&
											(isExpanded || isHovered || isMobileOpen)
										">
										<ul class="mt-2 space-y-1 ml-9">
											<li v-for="subItem in item.subItems" :key="subItem.name">
												<router-link
													:to="subItem.path"
													:class="[
														'menu-dropdown-item',
														{
															'menu-dropdown-item-active': isActive(
																subItem.path
															),
															'menu-dropdown-item-inactive': !isActive(
																subItem.path
															),
														},
													]">
													{{ subItem.name }}
													<span class="flex items-center gap-1 ml-auto">
														<span
															v-if="subItem.new"
															:class="[
																'menu-dropdown-badge',
																{
																	'menu-dropdown-badge-active': isActive(
																		subItem.path
																	),
																	'menu-dropdown-badge-inactive': !isActive(
																		subItem.path
																	),
																},
															]">
															new
														</span>
														<span
															v-if="subItem.pro"
															:class="[
																'menu-dropdown-badge',
																{
																	'menu-dropdown-badge-active': isActive(
																		subItem.path
																	),
																	'menu-dropdown-badge-inactive': !isActive(
																		subItem.path
																	),
																},
															]">
															pro
														</span>
													</span>
												</router-link>
											</li>
										</ul>
									</div>
								</transition>

								<!-- Subitem when collapse -->
								<transition
									@enter="startTransition"
									@after-enter="endTransition"
									@before-leave="startTransition"
									@after-leave="endTransition">
									<div
										v-show="!isExpanded && !isHovered && !isMobileOpen"
										class="mt-2 space-y-2" >
										<ul class="flex flex-col items-center gap-2 mr-1">
											<li v-for="subItem in item.subItems" :key="subItem.name">
												<router-link
													:to="subItem.path"
													:class="[
														'menu-dropdown-item transition-all',
														{
															'menu-dropdown-item-active': isActive(
																subItem.path
															),
															'menu-dropdown-item-inactive': !isActive(
																subItem.path
															),
														},
													]"
													:title="subItem.name">
													<component :is="subItem.icon" class="w-4 h-4 text-gray-500 dark:text-gray-400" />
												</router-link>
											</li>
										</ul>
									</div>
								</transition>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	</aside>
</template>
