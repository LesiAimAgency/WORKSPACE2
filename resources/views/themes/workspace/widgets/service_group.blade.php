@php
  $services = $settings['services'] ?? [];
@endphp

@if(!empty($services) && is_array($services))
  @foreach($services as $srv)
    @php
      $slug = Str::slug($srv['title'] ?? 'service');
      $workflowSettings = [
          'section_id' => $srv['workflow_section_id'] ?? ($slug === 'design' ? 'about' : $slug),
          'section_class' => 'workflow-' . $slug,
          'data_ui' => 'dark',
          'kicker' => $srv['workflow_kicker'] ?? 'We',
          'script' => $srv['title'] ?? 'Design',
          'suffix' => $srv['workflow_suffix'] ?? 'Spaces',
          'desc' => $srv['workflow_desc'] ?? ($srv['desc'] ?? ''),
          'image' => $srv['workflow_image'] ?? ($srv['image'] ?? asset('themes/workspace/assets/build1.png')),
          'nav_items' => [
              ['label' => 'What we do', 'is_current' => true, 'data_workflow_slide' => '0'],
              ['label' => 'Projects', 'is_current' => false, 'data_workflow_slide' => '1'],
              ['label' => 'About the expert', 'is_current' => false, 'data_workflow_slide' => '2'],
          ],
      ];

      $projectSettings = [
          'section_id' => $slug . '-project',
          'section_class' => 'project-' . $slug,
          'data_ui' => 'dark',
          'bg_image' => $srv['project_bg_image'] ?? asset('themes/workspace/assets/build1.png'),
          'projects' => $srv['projects'] ?? [],
          'nav_items' => [
              ['label' => 'What we do', 'is_current' => false],
              ['label' => 'Projects', 'is_current' => true],
              ['label' => 'About the expert', 'is_current' => false],
          ],
      ];

      $expertSettings = [
          'section_id' => $slug . '-expert',
          'section_class' => 'expert-' . $slug,
          'data_ui' => 'light',
          'heading' => $srv['expert_heading'] ?? "ABOUT THE<br />EXPERT",
          'image' => $srv['expert_image'] ?? asset('themes/workspace/assets/Man 1.png'),
          'bullets' => $srv['expert_bullets'] ?? [],
          'nav_items' => [
              ['label' => 'What we do', 'is_current' => false],
              ['label' => 'Projects', 'is_current' => false],
              ['label' => 'About the expert', 'is_current' => true],
          ],
      ];
    @endphp

    @include('themes.workspace.widgets.workflow', ['settings' => $workflowSettings])
    @include('themes.workspace.widgets.project', ['settings' => $projectSettings])
    @include('themes.workspace.widgets.expert', ['settings' => $expertSettings])
  @endforeach
@endif
