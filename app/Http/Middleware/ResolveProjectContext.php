<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Tenancy\DatabaseResolver;
use App\Services\Tenancy\ProjectContext;
use App\Services\Tenancy\ProjectResolver;
use App\Services\Tenancy\ThemeResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveProjectContext
{
    public function __construct(
        protected ProjectResolver $projectResolver,
        protected ProjectContext $projectContext,
        protected DatabaseResolver $databaseResolver,
        protected ThemeResolver $themeResolver
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $project = $this->projectResolver->resolve($request);

        if ($project) {
            $this->projectContext->setProject($project);
            $request->attributes->set('project', $project);

            // Connect project database
            $connection = $this->databaseResolver->connect($project);
            $this->projectContext->setConnection($connection);
        }

        // Always resolve active theme even if project is null
        $theme = $this->themeResolver->resolve($project);
        $this->projectContext->setTheme($theme);

        $response = $next($request);

        return $response instanceof Response ? $response : response($response);
    }

    /**
     * Handle cleanup after HTTP response is sent
     */
    public function terminate(Request $request, Response $response): void
    {
        $this->databaseResolver->disconnect();
        $this->projectContext->clear();
    }
}
