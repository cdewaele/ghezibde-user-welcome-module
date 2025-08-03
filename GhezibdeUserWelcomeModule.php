<?php

declare(strict_types=1);

namespace GhezibdeUserWelcome;

use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleBlockTrait;
use Fisharebest\Webtrees\Module\ModuleBlockInterface;
use Fisharebest\Webtrees\Auth;
use Fisharebest\Webtrees\Contracts\UserInterface;
use Fisharebest\Webtrees\Http\RequestHandlers\AccountEdit;
use Fisharebest\Webtrees\Individual;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Services\ModuleService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Str;
use Fisharebest\Webtrees\Module\ModuleInterface;
use Fisharebest\Webtrees\Module\ModuleChartInterface;
use Fisharebest\Webtrees\Module\PedigreeChartModule;
use Fisharebest\Webtrees\View;

use function e;
use function route;
use function view;

class GhezibdeUserWelcomeModule extends AbstractModule implements ModuleBlockInterface, ModuleCustomInterface
{
    use ModuleCustomTrait;
    use ModuleBlockTrait;

    private ModuleService $module_service;

    public function __construct(ModuleService $module_service)
    {
        $this->module_service = $module_service;
    }

    /**
     * Where does this module store its resources
     * 
     * @return string
     */
    public function resourcesFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    public function boot(): void
    {
        // Here is also a good place to register any views (templates) used by the module.
        // This command allows the module to use: view($this->name() . '::', 'fish')
        // to access the file ./resources/views/fish.phtml
        View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
    }

    public function title(): string
    {
        /* I18N: Name of a module */
        return I18N::translate('My Ghezibde');
    }

    public function description(): string
    {
        /* I18N: Description of the “My page” module */
        return I18N::translate('A greeting message and useful links for a Ghezibde user.');
    }

    public function customModuleAuthorName(): string
    {
        return 'Ghezibde';
    }

    public function customModuleVersion(): string
    {
        return '2.2.3.0';
    }

    public function customModuleLatestVersionUrl(): string
    {
        return 'https://github.com/cdewaele/ghezibde-user-welcome-module/raw/main/latest-version.txt';
    }


    /**
     * Generate the HTML content of this block.
     *
     * @param Tree                 $tree
     * @param int                  $block_id
     * @param string               $context
     * @param array<string,string> $config
     *
     * @return string
     */
    public function getBlock(Tree $tree, int $block_id, string $context, array $config = []): string
    {
        $gedcomid = $tree->getUserPreference(Auth::user(), UserInterface::PREF_TREE_ACCOUNT_XREF);

        $individual = Registry::individualFactory()->make($gedcomid, $tree);
        $links      = [];

        // $pedigree_chart = $this->module_service
        //     ->findByComponent(ModuleChartInterface::class, $tree, Auth::user())
        //     ->first(static fn(ModuleInterface $module): bool => $module instanceof PedigreeChartModule);

        if ($individual instanceof Individual) {
            // if ($pedigree_chart instanceof PedigreeChartModule) {
            //     $links[] = [
            //         'url'   => $pedigree_chart->chartUrl($individual),
            //         'title' => I18N::translate('Default chart'),
            //         'class' => 'icon-pedigree',
            //         'icon'  => view('icons/pedigree-right'),
            //     ];
            // }

            $links[] = [
                'url'   => $individual->url(),
                'title' => I18N::translate('My individual record'),
                'class' => 'icon-indis',
                'icon' => 'icon-indis',
            ];

            $links[] = [
                'url'   => 'https://www.ghezibde.net/genealogie/tree/vanderlynden.ged/ancestors-individuals-17/' . $gedcomid,
                'title' => I18N::translate('My ancestors'),
                'icon' => 'icon-pedigree',
            ];

            $links[] = [
                'url'   => 'https://www.ghezibde.net/genealogie/tree/vanderlynden.ged/webtrees-fan-chart/' . $gedcomid . '?generations=10&fanDegree=260&fontScale=80&hideEmptySegments=0&showColorGradients=1&showParentMarriageDates=1&innerArcs=3',
                'title' => I18N::translate('My fan tree'),
                'icon'  => 'icon-pedigree',
            ];
        }

        $links[] = [
            'url'   => route(AccountEdit::class, ['tree' => $tree->name()]),
            'title' => I18N::translate('My account'),
            'icon' => 'icon-user_add',
        ];

        $content = view($this->name() . '::welcome', ['links' => $links]);

        $real_name = "\u{2068}" . e(Auth::user()->realName()) . "\u{2069}";

        /* I18N: A %s is the user’s name */
        $title = I18N::translate('Welcome %s', $real_name);

        if ($context !== self::CONTEXT_EMBED) {
            return view('modules/block-template', [
                'block'      => Str::kebab($this->name()),
                'id'         => $block_id,
                'config_url' => '',
                'title'      => $title,
                'content'    => $content,
            ]);
        }

        return $content;
    }

    public function loadAjax(): bool
    {
        return false;
    }

    public function isUserBlock(): bool
    {
        return true;
    }

    public function isTreeBlock(): bool
    {
        return false;
    }

    /**
     * Additional/updated translations.
     *
     * @param string $language
     *
     * @return string[]
     */
    public function customTranslations(string $language): array
    {
        switch ($language) {
            case 'en-AU':
            case 'en-GB':
            case 'en-US':
                return $this->englishTranslations();

            case 'fr':
            case 'fr-CA':
                return $this->frenchTranslations();

            case 'nl':
                return $this->dutchTranslations();

            default:
                return [];
        }
    }

    /**
     * @return array<string,string>
     */
    protected function frenchTranslations(): array
    {
        return [
            'My Ghezibde'            => 'Mon Ghezibde',
            'My ancestors'           => 'Mes ancêtres',
            'My fan tree'            => 'Mon éventail',
            'My individual record'   => 'Ma fiche',
            'Welcome %s'             => 'Bienvenue %s',
        ];
    }

    protected function englishTranslations(): array
    {
        return [
            'My Ghezibde'            => 'My Ghezibde',
            'My ancestors'           => 'My ancestors',
            'My fan tree'            => 'My fan tree',
            'My individual record'   => 'My individual record',
            'Welcome %s'             => 'Welcome %s',
        ];
    }

    protected function dutchTranslations(): array
    {
        return [
            'My Ghezibde'            => 'Mijn Ghezibde',
            'My ancestors'           => 'Mijn voorouders',
            'My fan tree'            => 'Mijn waaierdiagram',
            'My individual record'   => 'Mijn persoonsfiche',
            'Welcome %s'             => 'Welkom %s',
        ];
    }
}
