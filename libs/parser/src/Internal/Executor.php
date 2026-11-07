<?php

declare(strict_types=1);

namespace TypeLang\Parser\Internal;

use TypeLang\Parser\Internal\Literal\BinIntParser;
use TypeLang\Parser\Internal\Literal\DecIntParser;
use TypeLang\Parser\Internal\Literal\DqStringParser;
use TypeLang\Parser\Internal\Literal\FloatParser;
use TypeLang\Parser\Internal\Literal\HexIntParser;
use TypeLang\Parser\Internal\Literal\OctIntParser;
use TypeLang\Parser\Internal\Literal\OctLegacyIntParser;
use TypeLang\Parser\Internal\Literal\SqStringParser;
use TypeLang\Parser\TypeParserFeatures;
use TypeLang\Type\TypeNode;

/**
 * @template-extends CompiledExecutor<TypeNode>
 */
final class Executor extends CompiledExecutor
{
    protected readonly DecIntParser $decInt;
    protected readonly HexIntParser $hexInt;
    protected readonly BinIntParser $binInt;
    protected readonly OctIntParser $octInt;
    protected readonly OctLegacyIntParser $octLegacyInt;
    protected readonly FloatParser $float;
    protected readonly DqStringParser $dqString;
    protected readonly SqStringParser $sqString;

    public function __construct(
        protected readonly TypeParserFeatures $features,
    ) {
        parent::__construct();

        $converter = BaseConverterFactory::default()
            ->select();

        $this->decInt = new DecIntParser($converter);
        $this->hexInt = new HexIntParser($converter);
        $this->binInt = new BinIntParser($converter);
        $this->octInt = new OctIntParser($converter);
        $this->octLegacyInt = new OctLegacyIntParser($converter);
        $this->float = new FloatParser();
        $this->dqString = new DqStringParser();
        $this->sqString = new SqStringParser();
    }
}
